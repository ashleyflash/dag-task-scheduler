<?php namespace DagTaskScheduler;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use League\Period\Period;

class SafeDateInterval {

    private static $formatters = [
        'second'    => 'Y-m-d H:i:s',
        'minute'    => 'Y-m-d H:i:00',
        'hour'      => 'Y-m-d H:00:00',
        'week'      => 'Y-m-d 00:00:00 - N \d\a\y + 1 \d\a\y',
        'day'       => 'Y-m-d 00:00:00',
        'month'     => 'Y-m-01 00:00:00',
        'year'      => 'Y-01-01 00:00:00',
    ];

    private $intervalString;

    /**
     * @param $interval
     */
    public function __construct($interval){

        if(!isset(static::$formatters[$interval])){
            throw new InvalidArgumentException("Invalid interval '$interval'. Expecting one of: " . implode(', ', array_keys(static::$formatters)));
        }

        $this->intervalString = $interval;
    }

    public function getFormat()
    {
        return static::$formatters[$this->intervalString];
    }

    public function getDateInterval()
    {
        return \DateInterval::createFromDateString("1 $this->intervalString");
    }
}

class PeriodHelper {

    /**
     * @param $interval
     * @return \DateInterval
     */
    public static function createSafeInterval($interval){

        return (new SafeDateInterval($interval))->getDateInterval();
    }

    /**
     * @param \DateTimeImmutable $start
     * @param \DateTimeImmutable $end
     * @param $interval
     * @param $timezone
     * @return \Generator
     */
    public static function createDateTimePeriod(\DateTimeImmutable $start, \DateTimeImmutable $end, $interval, $timezone)
    {
        $period = new Period(
            static::floorToNearestTimePeriod($start, $interval, $timezone),
            static::floorToNearestTimePeriod($end, $interval, $timezone)
        );

        $periods = $period->split(self::createSafeInterval($interval));

        foreach($periods as $p)
        {
            yield $p->endingOn($p->getEndDate()->modify('- 1 second'));
        }
    }

    /**
     * @param \DateTimeImmutable $time
     * @param $interval
     * @param $gmtOffset
     * @return bool|\DateTimeImmutable
     */
    public static function floorToNearestTimePeriod(\DateTimeImmutable $time, $interval, $gmtOffset)
    {
        $time = $time->setTimezone(static::getDateTimezoneFromOffset($gmtOffset));

        $format = (new SafeDateInterval($interval))->getFormat();

        $date = new \DateTimeImmutable($time->format($format), $time->getTimezone());

        return $date->setTimezone(new \DateTimeZone('UTC'));
    }

    /**
     * @param $gmtOffset
     * @return \DateTimeZone
     */
    private static function getDateTimezoneFromOffset($gmtOffset)
    {
        $diffString = ($gmtOffset > 0 ? '-':'+') . abs($gmtOffset);

        return new \DateTimeZone('Etc/GMT' . $diffString);
    }
}

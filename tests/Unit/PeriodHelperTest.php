<?php

use Flashtalking\DagTaskScheduler\PeriodHelper;

class PeriodHelperTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider providerFloorDates
     *
     * @param $start
     * @param $period
     * @param $expected
     * @param int $timezone
     */
    public function testItFloorsToNearestPeriod($start, $period, $expected, $timezone = 0)
    {
        $time = PeriodHelper::floorToNearestTimePeriod($start, $period, $timezone);

        $this->assertEquals($expected, $time);
    }

    public function testItThrowsAnExceptionForInvalidDateInterval()
    {
        $this->expectException('InvalidArgumentException');

        PeriodHelper::createSafeInterval('fail');
    }

    public function providerFloorDates()
    {
        return [
            [new DateTimeImmutable('2015-02-14 05:23:13'), 'minute', new DateTimeImmutable('2015-02-14 05:23:00')],
            [new DateTimeImmutable('2015-02-14 05:23:13'), 'hour', new DateTimeImmutable('2015-02-14 05:00:00')],
            [new DateTimeImmutable('2015-02-14 05:23:13'), 'day', new DateTimeImmutable('2015-02-14 00:00:00')],

            [new DateTimeImmutable('2015-02-14 04:59:59'), 'day', new DateTimeImmutable('2015-02-13 05:00:00'), -5],
            [new DateTimeImmutable('2015-02-14 05:00:00'), 'day', new DateTimeImmutable('2015-02-14 05:00:00'), -5],

            [new DateTimeImmutable('2015-02-13 22:59:59'), 'day', new DateTimeImmutable('2015-02-12 23:00:00'), 1],
            [new DateTimeImmutable('2015-02-13 23:00:00'), 'day', new DateTimeImmutable('2015-02-13 23:00:00'), 1],

            [new DateTimeImmutable('2015-02-14 13:59:59'), 'day', new DateTimeImmutable('2015-02-13 14:00:00'), 10],
            [new DateTimeImmutable('2015-02-14 14:00:00'), 'day', new DateTimeImmutable('2015-02-14 14:00:00'), 10],

            [new DateTimeImmutable('2015-02-14 05:23:13'), 'week', new DateTimeImmutable('2015-02-09 00:00:00')],
            [new DateTimeImmutable('2015-01-18 12:23:13'), 'week', new DateTimeImmutable('2015-01-12 00:00:00')],
            [new DateTimeImmutable('2015-04-21 05:23:13'), 'week', new DateTimeImmutable('2015-04-20 00:00:00')],

            [new DateTimeImmutable('2015-02-14 05:23:13'), 'month', new DateTimeImmutable('2015-02-01 00:00:00')],
            [new DateTimeImmutable('2015-02-14 05:23:13'), 'month', new DateTimeImmutable('2015-01-31 14:00:00'), 10],
            [new DateTimeImmutable('2015-02-14 05:23:13'), 'month', new DateTimeImmutable('2015-02-01 05:00:00'), -5],

            [new DateTimeImmutable('2015-02-14 05:23:13'), 'year', new DateTimeImmutable('2015-01-01 00:00:00')],
            [new DateTimeImmutable('2015-02-14 05:23:13'), 'year', new DateTimeImmutable('2015-01-01 05:00:00'), -5],
            [new DateTimeImmutable('2015-02-14 05:23:13'), 'year', new DateTimeImmutable('2014-12-31 23:00:00'), 1],
            [new DateTimeImmutable('2015-02-14 05:23:13'), 'year', new DateTimeImmutable('2014-12-31 14:00:00'), 10],
        ];
    }


    /**
     * @dataProvider providerPeriodDates
     *
     * @param $start
     * @param $end
     * @param $timezone
     * @param $range
     * @param $expected
     */
    public function testItCreatesScheduleRanges($start, $end, $timezone, $range, $expected)
    {
        $period = PeriodHelper::createDateTimePeriod(new DateTimeImmutable($start), new DateTimeImmutable($end), $range, $timezone);

        $this->assertEquals($expected, iterator_to_array($period));
    }

    public function providerPeriodDates()
    {
        return [
            # GMT 0 Offset

            /// Time now here          offset    last complete day here
            ['2014-02-17 00:00:00','2014-02-18 22:59:59', 0, 'day',
                [
                    new \League\Period\Period(new DateTimeImmutable('2014-02-17 00:00:00'), new DateTimeImmutable('2014-02-17 23:59:59')),
                ]
            ],
            ['2014-02-20 00:00:00','2014-02-21 23:59:59', 0, 'day',
                [
                    new \League\Period\Period(new DateTimeImmutable('2014-02-20 00:00:00'), new DateTimeImmutable('2014-02-20 23:59:59')),
                ]
            ],

            # GMT +10 Offset for Australia

            #['2015-02-11 14:00:00'
            ['2015-02-12 00:00:00','2015-02-14 15:59:59', 10, 'day',
                [
                    new \League\Period\Period(new DateTimeImmutable('2015-02-11 14:00:00'), new DateTimeImmutable('2015-02-12 13:59:59')),
                    new \League\Period\Period(new DateTimeImmutable('2015-02-12 14:00:00'), new DateTimeImmutable('2015-02-13 13:59:59')),
                    new \League\Period\Period(new DateTimeImmutable('2015-02-13 14:00:00'), new DateTimeImmutable('2015-02-14 13:59:59')),
                ]
            ],
            ['2014-02-17 13:59:59','2014-02-19 13:59:59', 10, 'day',
                [
                    new \League\Period\Period(new DateTimeImmutable('2014-02-16 14:00:00'), new DateTimeImmutable('2014-02-17 13:59:59')),
                    new \League\Period\Period(new DateTimeImmutable('2014-02-17 14:00:00'), new DateTimeImmutable('2014-02-18 13:59:59')),
                ]
            ],
            ['2014-02-17 13:59:59','2014-02-19 14:00:00', 10, 'day',
                [
                    new \League\Period\Period(new DateTimeImmutable('2014-02-16 14:00:00'), new DateTimeImmutable('2014-02-17 13:59:59')),
                    new \League\Period\Period(new DateTimeImmutable('2014-02-17 14:00:00'), new DateTimeImmutable('2014-02-18 13:59:59')),
                    new \League\Period\Period(new DateTimeImmutable('2014-02-18 14:00:00'), new DateTimeImmutable('2014-02-19 13:59:59')),
                ]
            ],

            # GMT -5 Offsets for U.S.

            ['2014-02-19 00:00:00','2014-02-21 06:59:59', -5, 'day',
                [
                    new \League\Period\Period(new DateTimeImmutable('2014-02-18 05:00:00'), new DateTimeImmutable('2014-02-19 04:59:59')),
                    new \League\Period\Period(new DateTimeImmutable('2014-02-19 05:00:00'), new DateTimeImmutable('2014-02-20 04:59:59')),
                    new \League\Period\Period(new DateTimeImmutable('2014-02-20 05:00:00'), new DateTimeImmutable('2014-02-21 04:59:59')),
                ]
            ],
            ['2014-02-19 00:00:00','2014-02-21 03:59:59', -5, 'day',
                [
                    new \League\Period\Period(new DateTimeImmutable('2014-02-18 05:00:00'), new DateTimeImmutable('2014-02-19 04:59:59')),
                    new \League\Period\Period(new DateTimeImmutable('2014-02-19 05:00:00'), new DateTimeImmutable('2014-02-20 04:59:59')),
                ]
            ],
            ['2014-02-19 00:00:00','2014-02-20 05:00:00', -5, 'day',
                [
                    new \League\Period\Period(new DateTimeImmutable('2014-02-18 05:00:00'), new DateTimeImmutable('2014-02-19 04:59:59')),
                    new \League\Period\Period(new DateTimeImmutable('2014-02-19 05:00:00'), new DateTimeImmutable('2014-02-20 04:59:59')),
                ]
            ],
            ['2014-02-19 00:00:00','2014-02-20 16:00:00', -5, 'day',
                [
                    new \League\Period\Period(new DateTimeImmutable('2014-02-18 05:00:00'), new DateTimeImmutable('2014-02-19 04:59:59')),
                    new \League\Period\Period(new DateTimeImmutable('2014-02-19 05:00:00'), new DateTimeImmutable('2014-02-20 04:59:59')),
                ]
            ],
            ['2014-02-19 00:00:00','2014-02-20 23:00:00', -5, 'day',
                [
                    new \League\Period\Period(new DateTimeImmutable('2014-02-18 05:00:00'), new DateTimeImmutable('2014-02-19 04:59:59')),
                    new \League\Period\Period(new DateTimeImmutable('2014-02-19 05:00:00'), new DateTimeImmutable('2014-02-20 04:59:59')),
                ]
            ],
            ['2014-02-18 04:59:59','2014-02-20 04:59:59', -5, 'day',
                [
                    new \League\Period\Period(new DateTimeImmutable('2014-02-17 05:00:00'), new DateTimeImmutable('2014-02-18 04:59:59')),
                    new \League\Period\Period(new DateTimeImmutable('2014-02-18 05:00:00'), new DateTimeImmutable('2014-02-19 04:59:59')),
                ]
            ],

            ['2016-02-02 04:59:59','2016-02-20 04:59:59', -5, 'week',
                [
                    new \League\Period\Period(new DateTimeImmutable('2016-02-01 05:00:00'), new DateTimeImmutable('2016-02-08 04:59:59')),
                    new \League\Period\Period(new DateTimeImmutable('2016-02-08 05:00:00'), new DateTimeImmutable('2016-02-15 04:59:59')),
                ]
            ],

            ['2016-02-04 14:59:59','2016-03-04 14:59:59', 10, 'month',
                [
                    new \League\Period\Period(new DateTimeImmutable('2016-01-31 14:00:00'), new DateTimeImmutable('2016-02-29 13:59:59')),
                ]
            ],

            ['2016-01-31 13:59:59','2016-02-31 13:59:59', 10, 'month',
                [
                    new \League\Period\Period(new DateTimeImmutable('2015-12-31 14:00:00'), new DateTimeImmutable('2016-01-31 13:59:59')),
                    new \League\Period\Period(new DateTimeImmutable('2016-01-31 14:00:00'), new DateTimeImmutable('2016-02-29 13:59:59')),
                ]
            ],
        ];
    }
}
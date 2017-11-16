<?php

class QueueHandlerTest extends \PHPUnit_Framework_TestCase {

    public function testItPushesJobToDefault()
    {
        $params = [
            'fizz' => 'buzz'
        ];

        $job = [
            'job_name' => 'foo',
            'queue' => null,
        ];

        $phresqueMock = $this->getMockBuilder('Phresque\Phresque')->disableOriginalConstructor()->getMock();
        $builderMock = $this->getMockBuilder('Phresque\Job\Builder')->disableOriginalConstructor()->getMock();


        $phresqueMock->method('make')->with('foo', $params)->willReturn($builderMock);
        $builderMock->method('lock')->willReturnSelf();
        $builderMock->method('push')->with('default')->willReturn("something");

        $queueHandler = new \Flashtalking\DagTaskScheduler\QueueHandler($phresqueMock);


        $return = $queueHandler->handle($job, $params);

        $this->assertEquals("something", $return);
    }
}
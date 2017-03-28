<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\web\Session;

class Locker extends Component
{
    /** @var $session Session*/
    protected $session;

    protected $waitTime = 300;

    protected $partTime = 4;

    public function init()
    {
        parent::init();
        $this->session = Yii::$app->session;
        $counter = $this->session->get('counter',0);
        $this->session->set('counter',$counter);
    }

    /**
     * reset counter value
     */
    public function resetCounter()
    {
        $this->session->set('counter', 0);
    }

    /**
     * get counter value
     */
    public function getCounter()
    {
        return $this->session->get('counter');
    }

    /**
     * pushing step in counter value
     */
    public function pushCounter()
    {
        $this->session->set('counter', $this->session->get('counter') + 1);
    }

    /**
     * checked counter value
     * @return bool
     */
    public function isBlocked()
    {
        return $this->session->get('counter') >= $this->partTime;
    }

    /**
     * start timer
     */
    public function startTimer(){
        $this->session->set('blocked_time',time());
    }

    /**
     * get remaining time
     * @return int
     */
    public function getTimer()
    {
        return $this->waitTime - (time() - $this->session->get('blocked_time'));
    }

    /**
     * timer start
     * @return bool
     */
    public function isStartTimer()
    {
        return !empty($this->session->get('blocked_time'));
    }

    /**
     * @param $time
     * @return array
     */
    public function formatTimer($time)
    {
        return ['minutes' => floor(($time % 3600)/60),'seconds'=>  $time % 60];
    }
}
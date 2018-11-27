<?php
error_reporting(E_ALL & ~E_NOTICE);
define('APP_PATH', dirname(__FILE__) . '/..');
require_once('/data/lib/iphp/loader.php');

class MyMasterWorker extends MasterWorker
{
	function master(){
		for($i=0; $i<100; $i++){
			#Logger::debug("add job $i");
			$this->add_job($i);
			#$this->wait(); // 如果每添加一个任务便 wait 的话, 将无法实现并发!
			#Logger::debug("");
		}
		Logger::debug("master added all $i jobs");
		
		// 当需要在确保所有任务处理完毕后再做其它操作时, 才需要调用 wait
		$this->wait();
		sleep(2);
		// ...
		Logger::debug("all job done");
	}

	function worker($job){
		usleep(mt_rand(1, 6) * 100 * 1000);
		// ...
		$pid = posix_getpid();
		Logger::debug("[$pid] process job: " . json_encode($job));
		return $pid;
	}

	//并行处理完成后的回调函数
	function call_back($job_result) {
		//在这里可以对并行处理结果进行汇总校验
		//或者在这里可以对并处理结果继续处理
		Logger::debug("job_result:" . json_encode($job_result));
	}
}

$mw = new MyMasterWorker();
$mw->set_num_workers(3);
//当call_back参数设置为true时需要定义call_back函数
$mw->run(true);



<?php
namespace LOL\Controller;

use Think\Controller;
use Think\Model;


class IndexController extends Controller
{
    public function index($id=5)
    {

        $this->display('Index/main');

    }

    /**
     * @description deal with the main input
     */
    public function displaySummonerInfo() {

        $serverName = I('post.serverName');
        $summonerName = I('post.summoner');
        $schoolName = I('post.schoolName');
        /*
         * TO DO
         * set input filter
         */
        if (empty($schoolName)) {
            echo "学校名不能为空";
            return;
        }
        if (empty($serverName)) {
            echo "服务器名不能为空";
            return;
        }
        if (empty($summonerName)) {
            echo "召唤师名不能为空";
            return;
        }

        $api = new \Common\Api;
        $summoner = array();
        $summoner['summoner_name'] = $summonerName;
        $summoner['server_name'] = $serverName;
        $summoner['school_name'] = $schoolName;

        //get summoner's info by api
        //to do.. summoner info empty judge
        $summonerInfo = $api->get_summoner_info($summonerName, $serverName);
        //dump($summonerInfo);

        $summonerRank = $api->get_summoner_rank($summonerName, $serverName);
        //dump($summonerRank);

        $summonerHiddenRank = $api->get_sommoner_hidden_rank($summonerName, $serverName);
        //dump($summonerHiddenRank);

        $summoner['rank'] = $summonerHiddenRank['rank6'];
        $summoner['rank_hidden'] = $summonerHiddenRank['rank'];


        foreach ($summoner as $key=>$value) {
            \Think\Log::record($key . " : " . $value, 'INFO');
        }
        $insertRes = $this->insertSummoner($summoner);
        if (empty($insertRes)) {
            \Think\Log::record("插入或更新成功", 'INFO');
        }


        $this->assign('summoner', $summoner);
        $this->display('Index/summoner_info');


    }


/*    public function displaySummonerInfo() {

        $summoner = I('post.');

        $this->assign('summoner', $summoner);
        $this->display('Index/summoner_info');

    }*/

    /**
     * @param $schoolName
     * @description get summoners' info in one school
     */
    public function getSchoolInfo($schoolName) {
        $summonerModel = M('Summoner');
        $whereSql = 'school_name = \'' . $schoolName . '\'';
        $data = $summonerModel->where($whereSql)
            ->order('rank_hidden desc')
            ->getField('summoner_name, server_name, rank, rank_hidden');

        $this->ajaxReturn($data);
    }




    /**
     * @param $summoner
     * @description insert summoner into db
     */
    private function insertSummoner($summoner) {
        $summoner['sys_flag'] = 1;
        $summoner['update_time'] = date("Y-m-d H:i:s" ,time());
        //dump($summoner);
        $summonerModel = M('Summoner');
        //dump($summonerModel->getDbFields());

        $whereSql = 'summoner_name = \'' . $summoner['summoner_name'] . '\' and server_name = \'' . $summoner['server_name'] . '\'';
        //dump($whereSql);
        $data = $summonerModel->where($whereSql)->select();
        //dump($data);

        if(count($data) != 0) {
            //update summoner
            $summonerModel->where($whereSql)->save($summoner);
            \Think\Log::record("更新", 'INFO');

        }
        else {
            //insert summoner
            $summonerModel->add($summoner);
            \Think\Log::record("插入", 'INFO');
        }

        return '';


    }
}
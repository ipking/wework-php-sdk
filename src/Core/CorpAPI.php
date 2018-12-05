<?php
namespace WeWork\Core;

use Exception;
use WeWork\Error\ParameterError;
use WeWork\Error\QyApiError;
use WeWork\Error\SysError;
use WeWork\Model\Agent;
use WeWork\Model\ApprovalData\ApprovalDataList;
use WeWork\Model\Batch\Batch;
use WeWork\Model\Batch\BatchJobArgs;
use WeWork\Model\CheckIn\CheckInOption;
use WeWork\Model\CheckInData\CheckInDataList;
use WeWork\Model\Department;
use WeWork\Model\Invoice\BatchGetInvoiceInfoReq;
use WeWork\Model\Invoice\BatchUpdateInvoiceStatusReq;
use WeWork\Model\Menu\Menu;
use WeWork\Model\Message\Message;
use WeWork\Model\Pay\PayWwSptrans2PocketReq;
use WeWork\Model\Pay\QueryWorkWxRedpackReq;
use WeWork\Model\Pay\QueryWwSptrans2PocketReq;
use WeWork\Model\Pay\SendWorkWxRedpackReq;
use WeWork\Model\Tag\Tag;
use WeWork\Model\User\User;
use WeWork\Model\User\UserDetailByUserTicket;
use WeWork\Model\User\UserInfoByCode;
use WeWork\Util\HttpUtils;
use WeWork\Util\Utils;

class CorpAPI extends API
{
    private $corpId = null;
    private $secret = null;
    protected $accessToken = null;
	
	/**
	 * @brief __construct : 构造函数，
	 * @note 企业进行自定义开发调用, 请传参 corpid + secret, 不用关心accesstoken，本类会自动获取并刷新
	 * @param null $corpId
	 * @param null $secret
	 * @throws \WeWork\Error\ParameterError
	 */
    public function __construct($corpId=null, $secret=null)
    {
        Utils::checkNotEmptyStr($corpId, "corpid");
        Utils::checkNotEmptyStr($secret, "secret");

        $this->corpId = $corpId;
        $this->secret = $secret;
    }


    // ------------------------- access token ---------------------------------
	
	/**
	 * @brief GetAccessToken : 获取 accesstoken，不用主动调用
	 * @return null : string accessToken
	 * @throws \WeWork\Error\ParameterError
	 */
	protected function GetAccessToken()
    {
        if ( ! Utils::notEmptyStr($this->accessToken)) { 
            $this->RefreshAccessToken();
        } 
        return $this->accessToken;
    }

    protected function RefreshAccessToken()
    {
        if (!Utils::notEmptyStr($this->corpId) || !Utils::notEmptyStr($this->secret))
            throw new ParameterError("invalid corpid or secret");

        $url = HttpUtils::MakeUrl(
            "/cgi-bin/gettoken?corpid={$this->corpId}&corpsecret={$this->secret}");
        $this->_HttpGetParseToJson($url, false);
        $this->_CheckErrCode();

        $this->accessToken = $this->rspJson["access_token"];
    }

    // ------------------------- 成员管理 -------------------------------------
    //
	/**
	 * @brief UserCreate : 创建成员
	 * @link https://work.weixin.qq.com/api/doc#10018
	 * @param \WeWork\Model\User\User $user : User
	 * @throws \WeWork\Error\QyApiError
	 */
    public function UserCreate(User $user)
    {
        User::CheckUserCreateArgs($user);
        $args = Utils::Object2Array($user);
        self::_HttpCall(self::USER_CREATE, 'POST', $args);
    }
	
	/**
	 * @brief UserGet : 读取成员
	 * @link https://work.weixin.qq.com/api/doc#10019
	 * @param $userid : string
	 * @return \WeWork\Model\User\User : User
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function UserGet($userid)
    {
        Utils::checkNotEmptyStr($userid, "userid"); 
        self::_HttpCall(self::USER_GET, 'GET', array('userid' => $userid)); 
        return User::Array2User($this->rspJson);
    }
	
	/**
	 * @brief UserUpdate : 更新成员
	 * @link https://work.weixin.qq.com/api/doc#10020
	 * @param \WeWork\Model\User\User $user : User
	 * @throws \WeWork\Error\QyApiError
	 */
    public function UserUpdate(User $user)
    {
        User::CheckUserUpdateArgs($user);
        $args = Utils::Object2Array($user);
        self::_HttpCall(self::USER_UPDATE, 'POST', $args);
    }
	
	/**
	 * @brief UserDelete : 删除成员
	 * @link https://work.weixin.qq.com/api/doc#10030
	 * @param $userid : string
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function UserDelete($userid)
    {
        Utils::checkNotEmptyStr($userid, "userid");
        self::_HttpCall(self::USER_DELETE, 'GET', array('userid' => $userid)); 
    }
	
	/**
	 * @brief UserBatchDelete : 批量删除成员
	 * @link https://work.weixin.qq.com/api/doc#10060
	 * @param array $userIdList : string array
	 * @throws \WeWork\Error\QyApiError
	 */
    public function UserBatchDelete(array $userIdList)
    {
        User::CheckUserBatchDeleteArgs($userIdList);
        $args = array("useridlist" => $userIdList);
        self::_HttpCall(self::USER_BATCH_DELETE, 'POST', $args);
    }
	
	/**
	 * @brief UserSimpleList : 获取部门成员
	 * @link https://work.weixin.qq.com/api/doc#10061
	 * @param $department_id
	 * @param $fetchChild : 1/0 是否递归获取子部门下面的成员
	 * @return array : User array
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function UserSimpleList($department_id, $fetchChild)
    {
        Utils::checkIsUInt($department_id, "department_id"); 
        self::_HttpCall(self::USER_SIMPLE_LIST, 'GET', array('department_id'=>$department_id, 'fetch_child'=>$fetchChild)); 
        return User::Array2UserList($this->rspJson);
    }
	
	/**
	 * @brief UserList : 获取部门成员详情
	 * @link https://work.weixin.qq.com/api/doc#10063
	 * @param $departmentId : uint
	 * @param $fetchChild : 1/0 是否递归获取子部门下面的成员
	 * @return array
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function UserList($departmentId, $fetchChild)
    {
        Utils::checkIsUInt($departmentId, "departmentId");
        self::_HttpCall(self::USER_LIST, 'GET', array('department_id'=>$departmentId, 'fetch_child'=>$fetchChild)); 
        return User::Array2UserList($this->rspJson);
    }
	
	/**
	 * @brief UserId2OpenId : userid转openid
	 * @link https://work.weixin.qq.com/api/doc#11279
	 * @param $userid : input string
	 * @param $openId : output string
	 * @param $agentid : intput string
	 * @param $appId : output string
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function UserId2OpenId($userid, &$openId, $agentid = null, &$appId = null)
    {
        Utils::checkNotEmptyStr($userid, "userid"); 
        if (is_null($agentid)) { 
            $args = array("userid" => $userid);
        } else { 
            $args = array("userid" => $userid, "agentid" => $agentid);
        } 
        self::_HttpCall(self::USERID_TO_OPENID, 'POST', $args); 
        $openId = Utils::arrayGet($this->rspJson, "openid");
        $appId = Utils::arrayGet($this->rspJson, "appid");
    }
	
	/**
	 * @brief openId2UserId : openid转userid
	 * @link https://work.weixin.qq.com/api/doc#11279
	 * @param $openId : intput string
	 * @param $userid : output string
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function openId2UserId($openId, &$userid)
    {
        Utils::checkNotEmptyStr($openId, "openid"); 
        $args = array("openid" => $openId);
        self::_HttpCall(self::OPENID_TO_USERID, 'POST', $args); 
        $userid= Utils::arrayGet($this->rspJson, "userid");
    }
	
	/**
	 * @brief UserAuthSuccess : 二次验证
	 * @link https://work.weixin.qq.com/api/doc#11378
	 * @param $userid : string
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function UserAuthSuccess($userid)
    {
        Utils::checkNotEmptyStr($userid, "userid");
        self::_HttpCall(self::USER_AUTH_SUCCESS, 'GET', array('userid'=>$userid));
    }

    // ------------------------- 部门管理 -------------------------------------
    //
	/**
	 * @brief DepartmentCreate : 创建部门
	 * @link https://work.weixin.qq.com/api/doc#10076
	 * @param \WeWork\Model\Department $department : Department
	 * @return null : uint departmentId
	 * @throws \WeWork\Error\QyApiError
	 */
    public function DepartmentCreate(Department $department)
    {
        Department::CheckDepartmentCreateArgs($department); 
        $args = Department::Department2Array($department);
        self::_HttpCall(self::DEPARTMENT_CREATE, 'POST', $args); 
        return Utils::arrayGet($this->rspJson, "id");
    }
	
	/**
	 * @brief DepartmentUpdate : 更新部门
	 * @link https://work.weixin.qq.com/api/doc#10077
	 * @param \WeWork\Model\Department $department : Department
	 * @throws \WeWork\Error\QyApiError
	 */
    public function DepartmentUpdate(Department $department)
    {
        Department::CheckDepartmentUpdateArgs($department); 
        $args = Department::Department2Array($department);
        self::_HttpCall(self::DEPARTMENT_UPDATE, 'POST', $args); 
    }
	
	/**
	 * @brief DepartmentDelete : 删除部门
	 * @link https://work.weixin.qq.com/api/doc#10079
	 * @param $departmentId : uint
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function DepartmentDelete($departmentId)
    {
        Utils::checkIsUInt($departmentId, "departmentId"); 
        self::_HttpCall(self::DEPARTMENT_DELETE, 'GET', array('id'=>$departmentId));
    }
	
	/**
	 * @brief DepartmentList : 获取部门列表
	 * @link https://work.weixin.qq.com/api/doc#10093
	 * @param $departmentId : uint, 如果不填，默认获取全量组织架构
	 * @return array : Department array
	 * @throws \WeWork\Error\QyApiError
	 */
    public function DepartmentList($departmentId = null)
    { 
        self::_HttpCall(self::DEPARTMENT_LIST, 'GET', array('id'=>$departmentId));
        return Department::Array2DepartmentList($this->rspJson);
    }

    //
    // ------------------------- 标签管理 -------------------------------------
    //
	/**
	 * @brief TagCreate : 创建标签
	 * @link https://work.weixin.qq.com/api/doc#10915
	 * @param \WeWork\Model\Tag\Tag $tag : Tag
	 * @return null : uint tagid
	 * @throws \WeWork\Error\QyApiError
	 */
    public function TagCreate(Tag $tag)
    {
        Tag::CheckTagCreateArgs($tag); 
        $args = Tag::Tag2Array($tag);
        self::_HttpCall(self::TAG_CREATE, 'POST', $args); 
        return Utils::arrayGet($this->rspJson, "tagid");
    }
	
	/**
	 * @brief TagUpdate : 更新标签名字
	 * @link https://work.weixin.qq.com/api/doc#10919
	 * @param \WeWork\Model\Tag\Tag $tag : Tag
	 * @throws \WeWork\Error\QyApiError
	 */
    public function TagUpdate(Tag $tag)
    {
        Tag::CheckTagUpdateArgs($tag); 
        $args = Tag::Tag2Array($tag);
        self::_HttpCall(self::TAG_UPDATE, 'POST', $args); 
    }
	
	/**
	 * @brief TagDelete : 删除标签
	 * @link https://work.weixin.qq.com/api/doc#10920
	 * @param $tagid : uint
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function TagDelete($tagid)
    {
        Utils::checkIsUInt($tagid, "tagid");
        self::_HttpCall(self::TAG_DELETE, 'GET', array('tagid'=>$tagid));
    }
	
	/**
	 * @brief TagGetUser : 获取标签成员
	 * @link https://work.weixin.qq.com/api/doc#10921
	 * @param $tagid : uint
	 * @return \WeWork\Model\Tag\Tag : Tag
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function TagGetUser($tagid)
    {
        Utils::checkIsUInt($tagid, "tagid");
        self::_HttpCall(self::TAG_GET_USER, 'GET', array('tagid'=>$tagid));
        return Tag::Array2Tag($this->rspJson);
    }
	
	/**
	 * @brief TagAddUser : 增加标签成员
	 * @link https://work.weixin.qq.com/api/doc#10923
	 * @param $tagid : uint
	 * @param array $userIdList : string array
	 * @param array $partyIdList : uint array
	 * @param &$invalidUserIdList : output string array
	 * @param &$invalidPartyIdList : output uint array
	 * @throws \WeWork\Error\QyApiError
	 * @note 1: userIdList/partyIdList 不能同时为空
	 * @note 2: 如果存在不合法的 userid/partyid, 不会throw Exception，但是会填充invalidUserIdList/invalidPartyIdList
	 */
    public function TagAddUser($tagid, $userIdList, $partyIdList, &$invalidUserIdList, &$invalidPartyIdList)
    {
        Tag::CheckTagAddUserArgs($tagid, $userIdList, $partyIdList); 
        $args = Tag::ToTagAddUserArray($tagid, $userIdList, $partyIdList);

        self::_HttpCall(self::TAG_ADD_USER, 'POST', $args); 

        $invalidUserIdList_string = utils::arrayGet($this->rspJson, "invalidlist");
        $invalidUserIdList = explode('|',$invalidUserIdList_string);
        $invalidPartyIdList = utils::arrayGet($this->rspJson, "invalidparty");
    }
	
	/**
	 * @brief TagDeleteUser : 删除标签成员
	 * @link https://work.weixin.qq.com/api/doc#10925
	 * @param $tagid : uint
	 * @param $userIdList : string array
	 * @param $partyIdList : uint array
	 * @param &$invalidUserIdList : output string array
	 * @param &$invalidPartyIdList : output uint array
	 * @throws \WeWork\Error\QyApiError
	 * @note 1: userIdList/partyIdList 不能同时为空
	 * @note 2: 如果存在不合法的 userid/partyid, 不会throw Exception，但是会填充invalidUserIdList/invalidPartyIdList
	 */
    public function TagDeleteUser($tagid, $userIdList, $partyIdList, &$invalidUserIdList, &$invalidPartyIdList)
    {
        Tag::CheckTagAddUserArgs($tagid, $userIdList, $partyIdList); 
        $args = Tag::ToTagAddUserArray($tagid, $userIdList, $partyIdList);

        self::_HttpCall(self::TAG_DELETE_USER, 'POST', $args); 

        $invalidUserIdList_string = utils::arrayGet($this->rspJson, "invalidlist");
        $invalidUserIdList = explode('|',$invalidUserIdList_string);
        $invalidPartyIdList = utils::arrayGet($this->rspJson, "invalidparty");
    }
	
	/**
	 * @brief TagGetList : 获取标签列表
	 * @link https://work.weixin.qq.com/api/doc#10926
	 * @return array : Tag array
	 * @throws \WeWork\Error\QyApiError
	 */
    public function TagGetList()
    {
        self::_HttpCall(self::TAG_GET_LIST, 'GET', array()); 
        return Tag::Array2TagList($this->rspJson);
    }

    //
    // ------------------------- 异步任务 -------------------------------------
    //

    private function BatchJob(BatchJobArgs $batchJobArgs, $jobType)
    {
        Batch::CheckBatchJobArgs($batchJobArgs);

        $args = Utils::Object2Array($batchJobArgs);

        $url = HttpUtils::MakeUrl("/cgi-bin/batch/{$jobType}?access_token=ACCESS_TOKEN");
        $this->_HttpPostParseToJson($url, $args);
        $this->_CheckErrCode();

        return Utils::arrayGet($this->rspJson, "jobid");
    }
	
	/**
	 * @brief BatchSyncUser : 增量更新成员
	 * @link https://work.weixin.qq.com/api/doc#10138/增量更新成员
	 * @param \WeWork\Model\Batch\BatchJobArgs $batchJobArgs : BatchJobArgs
	 * @return null : string jobid
	 */
    public function BatchSyncUser(BatchJobArgs $batchJobArgs)
    {
        return self::BatchJob($batchJobArgs, "syncuser");
    }
	
	/**
	 * @brief BatchReplaceUser : 全量覆盖成员
	 * @link https://work.weixin.qq.com/api/doc#10138/全量覆盖成员
	 * @param \WeWork\Model\Batch\BatchJobArgs $batchJobArgs : BatchJobArgs
	 * @return null : string jobid
	 */
    public function BatchReplaceUser(BatchJobArgs $batchJobArgs)
    {
        return self::BatchJob($batchJobArgs, "replaceuser");
    }
	
	/**
	 * @brief BatchReplaceParty : 全量覆盖部门
	 * @link https://work.weixin.qq.com/api/doc#10138/全量覆盖部门
	 * @param \WeWork\Model\Batch\BatchJobArgs $batchJobArgs : BatchJobArgs
	 * @return null : string jobid
	 */
    public function BatchReplaceParty(BatchJobArgs $batchJobArgs)
    {
        return self::BatchJob($batchJobArgs, "replaceparty");
    }
	
	/**
	 * @brief BatchJobGetResult : 获取异步任务结果
	 * @link https://work.weixin.qq.com/api/doc#10138/获取异步任务结果
	 * @param $jobId : string
	 * @return \WeWork\Model\Batch\BatchJobResult : BatchJobResult
	 * @throws \WeWork\Error\QyApiError
	 */
    public function BatchJobGetResult($jobId)
    {
        self::_HttpCall(self::BATCH_JOB_GET_RESULT, 'GET', array('jobid'=>$jobId)); 
        return Batch::Array2BatchJobResult($this->rspJson);
    }

    //
    // ------------------------- 邀请成员 --------------------------------------
    // 
	/**
	 * @brief BatchInvite : 邀请成员
	 * @link https://work.weixin.qq.com/api/doc#12543
	 * @param $userIdList : input string array
	 * @param $partyIdList : input uint array
	 * @param $tagIdList : input uint array
	 * @param $invalidUserIdList : output string array
	 * @param $invalidPartyIdList : output uint array
	 * @param $invalidTagIdList : output uint array
	 * @throws \WeWork\Error\QyApiError
	 */
	public function BatchInvite($userIdList, $partyIdList, $tagIdList, &$invalidUserIdList, &$invalidPartyIdList, &$invalidTagIdList){
		if(is_null($userIdList) && is_null($partyIdList) && is_null($tagIdList)){
			throw new QyApiError("input can not be all null");
		}
		$args = array('user' => $userIdList, 'party' => $partyIdList, 'tag' => $tagIdList);
		self::_HttpCall(self::BATCH_INVITE, 'POST', $args);
		
		$invalidUserIdList = Utils::arrayGet($this->rspJson, 'invaliduser');
		$invalidPartyIdList = Utils::arrayGet($this->rspJson, 'invalidparty');
		$invalidTagIdList = Utils::arrayGet($this->rspJson, 'invalidtag');
	}

    //
    // ------------------------- 应用管理 --------------------------------------
    //
	/**
	 * @brief AgentGet : 获取应用
	 * @link https://work.weixin.qq.com/api/doc#10087
	 * @param $agentid : string
	 * @return \WeWork\Model\Agent : Agent
	 * @throws \WeWork\Error\QyApiError
	 */
    public function AgentGet($agentid)
    {
        self::_HttpCall(self::AGENT_GET, 'GET', array('agentid'=>$agentid)); 
        return Agent::Array2Agent($this->rspJson);
    }
	
	/**
	 * @brief AgentSet : 设置应用
	 * @link https://work.weixin.qq.com/api/doc#10088
	 * @param $agent : Agent
	 * @throws \WeWork\Error\QyApiError
	 */
    public function AgentSet($agent)
    {
        Agent::CheckAgentSetArgs($agent); 
        $args = Agent::Agent2Array($agent);
        self::_HttpCall(self::AGENT_SET, 'POST', $args); 
    }
	
	/**
	 * @brief AgentList : 获取应用列表
	 * @link https://work.weixin.qq.com/api/doc#11214
	 * @return array : AgentList
	 * @throws \WeWork\Error\QyApiError
	 */
    public function AgentGetList()
    {
        self::_HttpCall(self::AGENT_GET_LIST, 'GET', array()); 
        return Agent::Array2AgentList($this->rspJson);
    }

    //
    // ------------------------- 自定义菜单 -----------------------------------
    //
    //
	/**
	 * @brief MenuCreate : 创建菜单
	 * @link https://work.weixin.qq.com/api/doc#10786
	 * @param $agentid : uint
	 * @param \WeWork\Model\Menu\Menu $menu : Menu
	 * @throws \WeWork\Error\QyApiError
	 */
    public function MenuCreate($agentid, Menu $menu)
    {
        Menu::CheckMenuCreateArgs($agentid, $menu); 
        $args = Utils::Object2Array($menu);
        self::_HttpCall(self::MENU_CREATE."&agentid={$agentid}", 'POST', $args); 
    }
	
	/**
	 * @brief MenuGet : 获取菜单
	 * @link https://work.weixin.qq.com/api/doc#10787
	 * @param $agentid : string
	 * @return \WeWork\Model\Menu\Menu : Menu
	 * @throws \WeWork\Error\QyApiError
	 */
    public function MenuGet($agentid)
    {
        self::_HttpCall(self::MENU_GET, 'GET', array('agentid'=>$agentid)); 
        return Menu::Array2Menu($this->rspJson);
    }
	
	/**
	 * @brief MenuGet : 删除菜单
	 * @link https://work.weixin.qq.com/api/doc#10788
	 * @param $agentid : string
	 * @throws \WeWork\Error\QyApiError
	 */
    public function MenuDelete($agentid)
    {
        self::_HttpCall(self::MENU_DELETE, 'GET', array('agentid'=>$agentid)); 
    }

    //
    // --------------------------- 消息推送 -----------------------------------
    //
    //
	/**
	 * @brief MessageSend : 发送消息
	 * @link https://work.weixin.qq.com/api/doc#10167
	 * @param \WeWork\Model\Message\Message $message : Message
	 * @param $invalidUserIdList : string array
	 * @param $invalidPartyIdList : uint array
	 * @param $invalidTagIdList : uint array
	 * @return void
	 * @throws \WeWork\Error\QyApiError
	 */
    public function MessageSend(Message $message, &$invalidUserIdList, &$invalidPartyIdList, &$invalidTagIdList)
    {
        $message->CheckMessageSendArgs(); 
        $args = $message->Message2Array();

        self::_HttpCall(self::MESSAGE_SEND, 'POST', $args); 

        $invalidUserIdList_string = utils::arrayGet($this->rspJson, "invaliduser");
        $invalidUserIdList = explode('|', $invalidUserIdList_string);

        $invalidPartyIdList_string = utils::arrayGet($this->rspJson, "invalidparty");
        $temp = explode('|', $invalidPartyIdList_string);
        foreach($temp as $item) {
            $invalidPartyIdList[] = intval($item);
        }

        $invalidTagIdList_string = utils::arrayGet($this->rspJson, "invalidtag");
        $temp = explode('|', $invalidTagIdList_string);
        foreach($temp as $item) {
            $invalidTagIdList[] = intval($item);
        }
    } 
    //
    // --------------------------- 素材管理 -----------------------------------
    //
    //
	/**
	 * @brief MediaUpload : 上传临时素材
	 * @link https://work.weixin.qq.com/api/doc#10112
	 * @param $filePath : string, 文件路径
	 * @param $type : string, 媒体文件类型，分别有图片（image）、语音（voice）、视频（video），普通文件（file）
	 * @return string media_id
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function MediaUpload($filePath, $type)
    {
        Utils::checkNotEmptyStr($filePath, "filePath");
        Utils::checkNotEmptyStr($type, "type");
        if ( ! file_exists($filePath)) { 
            throw new QyApiError("file not exists");
        }

        // 兼容php5.3-5.6 curl模块的上传操作
        if (class_exists('\CURLFile')) {
            $args = array('media' => new \CURLFile(realpath($filePath), 'application/octet-stream', basename($filePath)));
        } else {
            $args = array('media' => '@' . realpath($filePath));
        }

        $url = HttpUtils::MakeUrl("/cgi-bin/media/upload?access_token=ACCESS_TOKEN&type={$type}");
        $this->_HttpPostParseToJson($url, $args, true, true/*isPostFile*/);
        $this->_CheckErrCode();

        return $this->rspJson["media_id"];
    }

    public function MediaUploadByBuffer($buffer, $type)
    {
        $tmpPath = self::WriteTmpFile($buffer);

        try {
            $ret = $this->mediaUpload($tmpPath, $type);
            unlink($tmpPath);
            return $ret;
        }
        catch (Exception $ex) {
            unlink($tmpPath);
            throw $ex;
        }
    }
	
	/**
	 * @brief MediaGet : 获取临时素材
	 * @link https://work.weixin.qq.com/api/doc#10115
	 * @param $media_id : string
	 * @return null : string
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function MediaGet($media_id)
    {
        Utils::checkNotEmptyStr($media_id, "media_id"); 
        self::_HttpCall(self::MEDIA_GET, 'GET', array('media_id'=>$media_id));
        return $this->rspRawStr;
    }
	
	/**
	 * @brief MediaGet : 上传永久图片 。上传图片后，得到的图片永久URL。注意仅能用于图文消息（mpnews）正文中的图片展示
	 * @link https://work.weixin.qq.com/api/doc#13219
	 * @param $filePath : string, 图片文件路径
	 * @param $md5 : string, 图片文件的md5.可以不填
	 * @return string
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function UploadImage($filePath, $md5=null)
    {
        Utils::checkNotEmptyStr($filePath, "filePath");
        if ( ! file_exists($filePath)) { 
            throw new QyApiError("file not exists");
        }

        // 兼容php5.3-5.6 curl模块的上传操作
        if (class_exists('\CURLFile')) {
            $args = array('media' => new \CURLFile(realpath($filePath), 'application/octet-stream', basename($filePath)));
        } else {
            $args = array('media' => '@' . $filePath);//realpath($filePath));
        }

        var_dump($args);

        $url = HttpUtils::MakeUrl("/cgi-bin/media/uploadimg?access_token=ACCESS_TOKEN");
        if ($md5 != null) { 
            $url = $url . "&md5={$md5}";
        }

        $this->_HttpPostParseToJson($url, $args, true, true/*isPostFile*/);
        $this->_CheckErrCode();

        return $this->rspJson["url"];
    }

    //
    // --------------------------- 身份验证 -----------------------------------
    //
    //
	/**
	 * @brief GetUserInfoByCode : 根据code获取成员信息
	 * @link https://work.weixin.qq.com/api/doc#10028/根据code获取成员信息
	 * @param $code : string
	 * @return \WeWork\Model\User\UserInfoByCode : UserInfoByCode
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function GetUserInfoByCode($code)
    {
        Utils::checkNotEmptyStr($code, "code");
        self::_HttpCall(self::GET_USER_INFO_BY_CODE, 'GET', array('code'=>$code));
        return UserInfoByCode::Array2UserInfoByCode($this->rspJson);
    }
	
	/**
	 * @brief GetUserDetailByUserTicket : 使用user_ticket获取成员详情
	 * @link https://work.weixin.qq.com/api/doc#10028/使用user_ticket获取成员详情
	 * @param $ticket : string
	 * @return null : UserDetailByUserTicket
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function GetUserDetailByUserTicket($ticket)
    {
        Utils::checkNotEmptyStr($ticket, "ticket");
        $args = array("user_ticket" => $ticket);
        self::_HttpCall(self::GET_USER_DETAIL, 'POST', $args); 
        return UserDetailByUserTicket::Array2UserDetailByUserTicket($this->rspJson);
    }

    //
    // ---------------------- 移动端SDK ---------------------------------------
    //
    //
	/**
	 * @brief TicketGet : 获取电子发票ticket
	 * @link https://work.weixin.qq.com/api/doc#10029/获取电子发票ticket
	 * @return string ticket
	 * @throws \WeWork\Error\QyApiError
	 */
	public function TicketGet()
    {
        self::_HttpCall(self::GET_TICKET, 'GET', array('type'=>'wx_card'));
        return $this->rspJson["ticket"];
    }
	
	/**
	 * @brief JsApiTicketGet : 获取jsapi_ticket
	 * @link https://work.weixin.qq.com/api/doc#10029/获取jsapi_ticket
	 * @return  string ticket
	 * @throws \WeWork\Error\QyApiError
	 */
    public function JsApiTicketGet()
    {
        self::_HttpCall(self::GET_JSAPI_TICKET, 'GET', array()); 
        return $this->rspJson["ticket"];
    }
	
    /**
     * @brief JsApiSignatureGet : 计算jsapi的签名
     *
     * @link https://work.weixin.qq.com/api/doc#10029/%E7%AD%BE%E5%90%8D%E7%AE%97%E6%B3%95 
         *
     * @param $jsapiTicket : string
     * @param $nonceStr : string
     * @param $timestamp : string
     * @param $url : string
     *
     * @return string ticket
     */
    public function JsApiSignatureGet($jsapiTicket, $nonceStr, $timestamp, $url)
    {
    	$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        return sha1($string);
    }

    //
    // ---------------------- OA数据接口 --------------------------------------
    //
    //
	/**
	 * @brief CheckinOptionGet : 获取打卡规则
	 * @link https://work.weixin.qq.com/api/doc#12423
	 * @param $datetime : uint
	 * @param $useridlist : string array
	 * @return \WeWork\Model\CheckIn\CheckInOption : CheckinOption
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function CheckinOptionGet($datetime, $useridlist)
    { 
        Utils::checkIsUInt($datetime, "datetime");
        Utils::checkNotEmptyArray($useridlist, "useridlist");
        if (count($useridlist) > 100) throw new QyApiError("no more than 100 user once"); 
        $args = array("datetime" => $datetime, "useridlist" => $useridlist);

        self::_HttpCall(self::GET_CHECKIN_OPTION, 'POST', $args); 

        return CheckInOption::ParseFromArray($this->rspJson);
    }
	
	/**
	 * @brief CheckInDataGet : 获取打卡数据
	 * @link https://work.weixin.qq.com/api/doc#11196
	 * @param $opencheckindatatype : uint
	 * @param $starttime : uint
	 * @param $endtime : uint
	 * @param $useridlist : string array
	 * @return \WeWork\Model\CheckInData\CheckInDataList : CheckInDataList
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function CheckinDataGet($opencheckindatatype, $starttime, $endtime, $useridlist)
    { 
        Utils::checkIsUInt($opencheckindatatype, "opencheckindatatype");
        Utils::checkIsUInt($starttime, "starttime");
        Utils::checkIsUInt($endtime, "endtime");
        Utils::checkNotEmptyArray($useridlist, "useridlist");
        if (count($useridlist) > 100) throw new QyApiError("no more than 100 user once");

        $args = array(
            "opencheckindatatype" => $opencheckindatatype, 
            "starttime" => $starttime,
            "endtime" => $endtime,
            "useridlist" => $useridlist,
        );
        self::_HttpCall(self::GET_CHECKIN_DATA, 'POST', $args); 
        return CheckInDataList::ParseFromArray($this->rspJson);
    }
	
	/**
	 * @brief ApprovalDataGet : 获取审批数据
	 * @link https://work.weixin.qq.com/api/doc#11228
	 * @param $starttime : uint
	 * @param $endtime : uint
	 * @param $next_spnum : uint
	 * @return \WeWork\Model\ApprovalData\ApprovalDataList : ApprovalDataList
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function ApprovalDataGet($starttime, $endtime, $next_spnum=null)
    { 
        Utils::checkIsUInt($starttime, "starttime");
        Utils::checkIsUInt($endtime, "endtime");

        $args = array();
        Utils::setIfNotNull($starttime, "starttime", $args);
        Utils::setIfNotNull($endtime, "endtime", $args);
        Utils::setIfNotNull($next_spnum, "next_spnum", $args);

        self::_HttpCall(self::GET_APPROVAL_DATA, 'POST', $args); 
        return ApprovalDataList::ParseFromArray($this->rspJson);
    }

    //
    // ---------------------- 企业支付 ----------------------------------------
    //
    static private function _HttpPostXml($url, $args)
    {
        $postData = Utils::Array2Xml("xml", $args);
        return Utils::Xml2Array(HttpUtils::httpPost($url, $postData));
    } 
    static private function _CheckXmlRetCode($rsp)
    {
        if ($rsp["return_code"] != "SUCCESS") { 
            throw new QyApiError("response error:" . $rsp);
        }
    }
	
	/**
	 * @brief SendWorkWxRedpack : 发放企业红包
	 * @link https://work.weixin.qq.com/api/doc#11543
	 * @param \WeWork\Model\Pay\SendWorkWxRedpackReq $SendWorkWxRedpackReq
	 * @return mixed : SendWorkWxRedpackRsp
	 * @throws \WeWork\Error\QyApiError
	 * @note : 本接口只检查通信是否正常，业务结果需调用方自行判断，参看文档
	 */
	static public function SendWorkWxRedpack(SendWorkWxRedpackReq $SendWorkWxRedpackReq)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendworkwxredpack"; 
        $args = Utils::Object2Array($SendWorkWxRedpackReq);
        $SendWorkWxRedpackRsp = self::_HttpPostXml($url, $args); 
        self::_CheckXmlRetCode($SendWorkWxRedpackRsp);
        return $SendWorkWxRedpackRsp;
    }
	
	/**
	 * @brief QueryWorkWxRedpack : 查询红包记录
	 * @link https://work.weixin.qq.com/api/doc#11544
	 * @param \WeWork\Model\Pay\QueryWorkWxRedpackReq $QueryWorkWxRedpackReq
	 * @return mixed : QueryWorkWxRedpackRsp
	 * @throws \WeWork\Error\QyApiError
	 */
    static public function QueryWorkWxRedpack(QueryWorkWxRedpackReq $QueryWorkWxRedpackReq)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/queryworkwxredpack"; 
        $args = Utils::Object2Array($QueryWorkWxRedpackReq);
        $QueryWorkWxRedpackRsp = self::_HttpPostXml($url, $args); 
        self::_CheckXmlRetCode($QueryWorkWxRedpackRsp);
        return $QueryWorkWxRedpackRsp;
    }
	
	/**
	 * @brief PayWwSptrans2Pocket : 向员工付款
	 * @link https://work.weixin.qq.com/api/doc#11545
	 * @param \WeWork\Model\Pay\PayWwSptrans2PocketReq $PayWwSptrans2PocketReq
	 * @return mixed : PayWwSptrans2PocketRsp
	 * @throws \WeWork\Error\QyApiError
	 */
    static public function PayWwSptrans2Pocket(PayWwSptrans2PocketReq $PayWwSptrans2PocketReq)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/paywwsptrans2pocket"; 
        $args = Utils::Object2Array($PayWwSptrans2PocketReq);
        $PayWwSptrans2PocketRsp = self::_HttpPostXml($url, $args); 
        self::_CheckXmlRetCode($PayWwSptrans2PocketRsp);
        return $PayWwSptrans2PocketRsp;
    }
	
	/**
	 * @brief QueryWwSptrans2Pocket : 查询付款记录
	 * @link https://work.weixin.qq.com/api/doc#11546
	 * @param \WeWork\Model\Pay\QueryWwSptrans2PocketReq $QueryWwSptrans2PocketReq
	 * @return mixed : QueryWwSptrans2PocketRsp
	 * @throws \WeWork\Error\QyApiError
	 */
    static public function QueryWwSptrans2Pocket(QueryWwSptrans2PocketReq $QueryWwSptrans2PocketReq)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/querywwsptrans2pocket"; 
        $args = Utils::Object2Array($QueryWwSptrans2PocketReq);
        $QueryWwSptrans2Pocketsp = self::_HttpPostXml($url, $args); 
        self::_CheckXmlRetCode($QueryWwSptrans2Pocketsp);
        return $QueryWwSptrans2Pocketsp;
    }
    //
    // ---------------------- 电子发票 ----------------------------------------
    //
    //
	/**
	 * @brief GetInvoiceInfo : 查询电子发票
	 * @link https://work.weixin.qq.com/api/doc#11631
	 * @param $card_id : string
	 * @param $encrypt_code : string
	 * @return object : InvoiceInfo
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function GetInvoiceInfo($card_id, $encrypt_code)
    { 
        Utils::checkNotEmptyStr($card_id, "card_id");
        Utils::checkNotEmptyStr($encrypt_code, "encrypt_code"); 
        $args = array("card_id" => $card_id, "encrypt_code" => $encrypt_code); 
        self::_HttpCall(self::GET_INVOICE_INFO, 'POST', $args); 
        return Utils::Array2Object($this->rspJson);
    }
	
	/**
	 * @brief UpdateInvoiceStatus : 更新发票状态
	 * @link https://work.weixin.qq.com/api/doc#11633
	 * @param $card_id : string
	 * @param $encrypt_code : string
	 * @param $reimburse_status : string
	 * @return void
	 * @throws \WeWork\Error\ParameterError
	 * @throws \WeWork\Error\QyApiError
	 */
    public function UpdateInvoiceStatus($card_id, $encrypt_code, $reimburse_status)
    { 
        Utils::checkNotEmptyStr($card_id, "card_id");
        Utils::checkNotEmptyStr($encrypt_code, "encrypt_code");
        Utils::checkNotEmptyStr($reimburse_status, "reimburse_status"); 
        $args = array("card_id" => $card_id, "encrypt_code" => $encrypt_code, "reimburse_status" => $reimburse_status); 
        self::_HttpCall(self::UPDATE_INVOICE_STATUS, 'POST', $args); 
    }
	
	/**
	 * @brief BatchUpdateInvoiceStatus : 批量更新发票状态
	 * @link https://work.weixin.qq.com/api/doc#11634
	 * @param $BatchUpdateInvoiceStatusReq
	 * @throws \WeWork\Error\QyApiError
	 */
	public function BatchUpdateInvoiceStatus(BatchUpdateInvoiceStatusReq $BatchUpdateInvoiceStatusReq)
    { 
        $args = Utils::Object2Array($BatchUpdateInvoiceStatusReq); 
        self::_HttpCall(self::BATCH_UPDATE_INVOICE_STATUS, 'POST', $args); 
    }
	
	/**
	 * @brief BatchGetInvoiceInfo : 批量查询电子发票
	 * @link https://work.weixin.qq.com/api/doc#11974
	 * @param \WeWork\Model\Invoice\BatchGetInvoiceInfoReq $BatchGetInvoiceInfoReq
	 * @return object : BatchGetInvoiceInfoRsp
	 * @throws \WeWork\Error\QyApiError
	 */
    public function BatchGetInvoiceInfo(BatchGetInvoiceInfoReq $BatchGetInvoiceInfoReq)
    { 
        $args = Utils::Object2Array($BatchGetInvoiceInfoReq); 
        self::_HttpCall(self::BATCH_GET_INVOICE_INFO, 'POST', $args); 
        return Utils::Array2Object($this->rspJson);
    }

    //
    // ------------------------- private --------------------------------------
    //

    static private function WriteTmpFile($buffer)
    {
        Utils::checkNotEmptyStr($buffer, "buffer");

        $tmpPath = tempnam(sys_get_temp_dir(), "qytmpfile");
        $handle = null;

        try {
            $handle = fopen($tmpPath, "wb");
            if ($handle === false) {
                throw new SysError("create tmp file failed");
            }

            $writeBytes = fwrite($handle, $buffer);
            if ($writeBytes === false) {
                throw new SysError("write tmp file failed");
            }
            while ($writeBytes < count($buffer)) {
                $n = fwrite($handle, substr($buffer, $writeBytes));
                if ($n === false) {
                    throw new SysError("write tmp file failed");
                }
                $writeBytes += $n;
            }
            fclose($handle);
        }
        catch (Exception $ex) {
            if (!is_null($handle)) {
                fclose($handle);
                unlink($tmpPath);
            }
            throw $ex;
        }
        return $tmpPath;
    }
}

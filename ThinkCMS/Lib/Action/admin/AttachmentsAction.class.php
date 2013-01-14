<?php

//附件管理
class AttachmentsAction extends GlobalAction {

    protected $filePath = '';
    protected $thumbPath = '';
    protected $ModelId = 'b1a9de09-5199-11e2-83b4-f0def10abaa2';

    /*
     * 初始化
     */

    public function _initialize() {
        parent::_initialize();
    }

    /*
     * 上传图片
     */

    public function upload() {
        $fileType = trim($_REQUEST['type']);
        $this->filePath = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . '/uploadfile/' . $fileType . '/' . date('Y') . '/' . date('m') . '/');
        $this->thumbPath = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . '/uploadfile/' . $fileType . '/thumb/' . date('Y') . '/' . date('m') . '/');
        $description = htmlspecialchars($_POST['pictitle'], ENT_QUOTES);
        $res = $this->saveUploadFile($fileType, $description, true);
        exit(json_encode($res));
    }

    /*
     * 涂鸦，手写
     */

    public function scrawl() {
        //临时文件目录
        $tmpPath = './uploadfile/temp/';
        $fileType = 'image';
        $this->filePath = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $tmpPath);
        $this->thumbPath = $this->filePath;
//获取当前上传的类型
        $action = htmlspecialchars($_GET["action"]);
        if ($action == "tmpImg") { // 背景上传
            //背景保存在临时目录中
            $info = $this->saveUploadFile('image');
            /**
             * 返回数据，调用父页面的ue_callback回调
             */
            exit("<script>parent.ue_callback('" . $info["url"] . "','" . $info["state"] . "')</script>");
        } else {
            $this->filePath = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . '/uploadfile/' . $fileType . '/' . date('Y') . '/' . date('m') . '/');
            $info = $this->saveUploadFile($fileType, '', false, true);
            //上传成功后删除临时目录
            if (file_exists($tmpPath)) {
                delDir($tmpPath);
            }exit(json_encode($info));
        }
    }

    /*
     * 保存附件
     */

    protected function saveUploadFile($fileType, $description = '', $warter = false, $base64 = false) {
        switch ($fileType) {
            case 'image'://图片
                $maxSize = 10485760; //10M
                $allowExts = array('jpg', 'gif', 'png', 'jpeg'); //允许的扩展名
                $warter = true;
                break;
            case 'move'://视频
                $maxSize = 104857600; //100M
                $allowExts = array('fli', 'flc', 'mpg', 'mov', 'msl', 'avi', 'mpeg', 'qt', 'rm', 'ram'); //允许的扩展名
            case 'music'://音乐
                $maxSize = 31457280; //30M
                $allowExts = array('mp3', 'wav', 'aac', 'm4a', 'mid', 'aif'); //允许的扩展名
            case 'attachment'://音乐
                $maxSize = 31457280; //30M
                $allowExts = array('zip', 'rar', '7z', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'wps'); //允许的扩展名
            default:
                $maxSize = 1; //
                $allowExts = array();
                break;
        }
        $filePath = $this->filePath;
        $thumbPath = $this->thumbPath;
        if (!file_exists($filePath)) {
            mk_dirs($filePath);
        }
        if (!file_exists($thumbPath)) {
            mk_dirs($thumbPath);
        }
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();
        $upload->maxSize = $maxSize;
        $upload->allowExts = $allowExts;
        $upload->savePath = $filePath;
        $upload->saveRule = 'uniqid';
        $upload->thumb = C('IS_THUMB');
        $upload->thumbMaxWidth = C('THUMB_MAX_WIDTH');
        $upload->thumbMaxHeight = C('THUMB_MAX_HEIGHT');
        $upload->thumbPrefix = '';
        $upload->thumbPath = $thumbPath;
        if ($warter) {
            $upload->isWater = true;
            $upload->waterFile = APP_PATH . 'Tpl/' . GROUP_NAME . '/Public/images/logo.png';
        }
        if ($base64) {
            $upload->base64Data = true;
            $upload->base64Field = 'content';
        }
        $res = array();
        if (!$upload->upload()) {
            //捕获上传异常
            $res['state'] = $upload->getErrorMsg();
            return $res;
        } else {
            //上传成功
            $uploadList = $upload->getUploadFileInfo();
            $res['originalName'] = $uploadList[0]['name'];
            $res['name'] = $uploadList[0]['savename'];
            $res['title'] = $description;
            $res['url'] = C('ATTH_PATH') . str_replace('uploadfile/', '', str_replace($_SERVER['DOCUMENT_ROOT'], '', $uploadList[0]['savepath'])) . $uploadList[0]['savename'];
            $res['size'] = $uploadList[0]['size'];
            $res['type'] = '.' . $uploadList[0]['extension'];
            $res['state'] = 'SUCCESS';

            //保存附件
            $Dao = M('Attachments');
            $data = array();
            $data['attachments_name'] = $uploadList[0]['name']; //原始文件名
            $data['description'] = $description; //
            $data['filename'] = $res['url']; //修改后的文件名
            $data['user_id'] = $this->UserID;
            $data['size'] = $uploadList[0]['size'];
            $data['create_time'] = time();
            $data['listorder'] = 0;
            $Dao->data($data)->add();
            $res['aid'] = $Dao->getLastInsID(); //保存的附件ID
            return $res;
        }
    }

    /**
     * 返回附件列表 —— json 方式
     */
    public function getAttachements_json() {
        $attIds = $_REQUEST['attIds'];
        $attachments = getAttachments($attIds, $this->UserID);
        if (count($attachments) > 0) {
            $result = array(
                'count' => count($attachments),
                'items' => $attachments
            );
            echo(json_encode($result));
        } else {
            echo('{count:0;items:[]}');
        }
    }

}
<?php
namespace backend\demo\controller;

use think\Controller;

class Upload extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function uploadApi()
    {
        // ��ȡ���ϴ��ļ� �����ϴ���001.jpg
        $file = request()->file('image');

        // �ƶ������Ӧ�ø�Ŀ¼/public/uploads/ Ŀ¼��
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // �ɹ��ϴ��� ��ȡ�ϴ���Ϣ
                // ��� jpg
                echo $info->getExtension();
                // ��� 20160820/42a79759f284b767dfcb2a0197904287.jpg
                echo $info->getSaveName();
                // ��� 42a79759f284b767dfcb2a0197904287.jpg
                echo $info->getFilename();
            }else{
                // �ϴ�ʧ�ܻ�ȡ������Ϣ
                echo $file->getError();
            }
        }
    }
}
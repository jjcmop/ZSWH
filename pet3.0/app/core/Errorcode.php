<?php
namespace app\core;
/**
 * 错误码设置类
 */
class Errorcode {
    /**
     * 获取错误码配置
     */
    public static function getCodeConfig() {
        return array(
            "0"=>"success",
            "100"=>"签名错误",
            "101"=>"缺少签名",
            "102"=>"缺少参数",
            "103"=>"token失效，请登录",
            "104"=>"请求方式错误",
            "105"=>"格式错误",
            "106"=>"缺少文件",
            "1000"=>"请输入正确的手机号",
            "1001"=>"获取验证码频率过高，请稍后",
            "1002"=>'验证码错误',
            "1003"=>'手机号已注册，请直接登入',
            "1004"=>'手机号不存在，请注册',
            "1005"=>'验证码过期',
            "1006" =>"注册失败，请稍后再试",
            "1007"=>"账号密码错误，请稍后再试",
            "1008"=>"没有找到此私有标签",
            "1009"=>'标签为空',
            "1010"=>'缺少品种ID',
            "1011"=>'没有此品种',
            "1012"=>'没有此文章',
            "1013"=>'没有此用户',
            "1014"=>'微信号已绑定，请重新认证',
            "1100"=>'没有找到此帖',
            "1102"=>'帖子内容过长',
            "1201"=>'缺少文章ID',
            "1300"=>'没有找到此公益',
            "1301"=>'没有找到用户信息',
            "1302"=>'fail',
            "1303"=>'没有找到新宠友申请',
            "1400"=>'缺少地图ID',
            "1500"=>'群名称重复',
            "1501"=>'群组已达到上限，请重新选择',
            "1502"=>'该用户已是群主,请重新加群',
            "500"=>"系统繁忙，请稍候再试"
        );
    }

}
<?php

namespace Callback\Controller;
use Think\Controller;
use Common\Api\GameApi;
/**
 * 支付回调控制器
 * @author 小纯洁 
 */
class BaseController extends Controller {

    /**
    *充值到游戏成功后修改充值状态和设置游戏币
    */
    protected function set_spend($data){
        $spend = M('Spend',"tab_");
        $map['pay_order_number'] = $data['out_trade_no'];
        $d = $spend->where($map)->find();
        if(empty($d)){$this->record_logs("数据异常");return false;}
        if($d['pay_status'] == 0){
            $data_save['pay_status'] = 1;
            $data_save['order_number'] = $data['trade_no'];
            $map_s['pay_order_number'] = $data['out_trade_no']; 
            $r = $spend->where($map_s)->save($data_save);
            $this->set_ratio($d['pay_order_number']);
            if($r!== false){
                $game = new GameApi();
                $game->game_pay_notify($data,1);
                return true;
            }else{
                $this->record_logs("修改数据失败");
            }
        }
        else{
            return true;
        }
    }

    /**
    *充值平台币成功后的设置
    */
    protected function set_deposit($data){
        $deposit = M('deposit',"tab_");
        $map['pay_order_number'] = $data['out_trade_no'];
        $d = $deposit->where($map)->find();
        if(empty($d)){return false;}
        if($d['pay_status'] == 0){
            $data_save['pay_status'] = 1;
            $data_save['order_number'] = $data['trade_no'];
            $map_s['pay_order_number'] = $data['out_trade_no'];
            $r = $deposit->where($map_s)->save($data_save);
            if($r !== false){
                //积分获取记录
                $wheresign['name']='ALIPAY_POINT_SIGN';
                $alipay_points_sign=M('config','sys_')->where($wheresign)->getfield('value');
                $datasave['user_id']=$d['user_id'];
                $datasave['user_account']=$d['user_account'];
                $datasave['amount']=$d['pay_amount'];
                $datasave['pay_way']=$d['pay_way'];
                $datasave['create_time']=time();
                $points=(int)($d['pay_amount'] * $alipay_points_sign);
                $datasave['points']=$points;
                M('points_record','tab_')->add($datasave);
                $user = M("user","tab_");
                $user->where("id=".$d['user_id'])->setInc("balance",$d['pay_amount']);
                $user->where("id=".$d['user_id'])->setInc("cumulative",$d['pay_amount']);
                //把用户的积分加上
                $user->where("id=".$d['user_id'])->setInc("points",$points);
            }else{
                $this->record_logs("修改数据失败");
            }
            return true;
        }
        else{
            return true;
        }
    }
    /**
     *渠道充值平台币成功后的设置
     */
    protected function set_promoteDeposit($data){
        $promote_deposit = M('promote_deposit',"tab_");
        $map['pay_order_number'] = $data['out_trade_no'];
        $d = $promote_deposit->where($map)->find();
        if(empty($d)){return false;}
        if($d['pay_status'] == 0){
            $data_save['pay_status'] = 1;
            $data_save['order_number'] = $data['trade_no'];
            $map_s['pay_order_number'] = $data['out_trade_no'];
            $r = $promote_deposit->where($map_s)->save($data_save);
            if($r !== false){
                $promote = M("promote","tab_");
                $promote->where("id=".$d['promote_id'])->setDec("alipay_limit",$d['pay_amount']);
                $promote->where("id=".$d['promote_id'])->setInc("balance_coin",$d['pay_amount']);

            }else{
                $this->record_logs("修改数据失败");
            }
            return true;
        }
        else{
            return true;
        }
    }

    /**
    *设置代充数据信息
    */
    protected function set_agent($data){
        $agent = M("agent","tab_");
        $map['pay_order_number'] = $data['out_trade_no'];
        $d = $agent->where($map)->find();
        if(empty($d)){return false;}
        if($d['pay_status'] == 0){
            $data_save['pay_status'] = 1;
            $data_save['order_number'] = $data['trade_no'];
            $map_s['pay_order_number'] = $data['out_trade_no'];
            $r = $agent->where($map_s)->save($data_save);
            if($r!== false){
               
                $map_play['game_id'] = $d['game_id'];
                if($d['account_type'] == 1){
                     $user = M("UserPlay","tab_");
                     $map_play['user_id'] = $d['user_id'];
                    $user->where($map_play)->setInc("bind_balance",$d['amount']);
                }else{
                    //如果渠道无游戏记录数据则添加数据后在转移
                    $promotegame = M('PromoteGame','tab_');
                    $map_play['promote_id'] = $d['user_id'];
                    $select_res = $promotegame->where($map_play)->getfield('id');
                    if($select_res){
                        $promotegame->where($map_play)->setInc("bind_balance",$d['amount']);
                    }else{
                        $map_promote['id'] = $map_play['promote_id'];
                        $promote_info = M('Promote','tab_')->where($map_promote)->find();
                        $map_game['id'] = $map_play['game_id'];
                        $game_info = M('Promote','tab_')->where($map_game)->find();
                        $promote_game_data['game_id'] = $map_play['game_id'];
                        $promote_game_data['game_name'] = $game_info['game_name'];
                        $promote_game_data['promote_id'] = $map_play['promote_id'];
                        $promote_game_data['promote_account'] = $promote_info['account'];
                        $promote_game_data['promote_nickname'] = $promote_info['nickname'];
                        $add_res = $promotegame->add(promote_game_data);
                        if($add_res){
                           $promotegame->where($map_play)->setInc("bind_balance",$d['amount']); 
                       }else{
                             $this->record_logs("修改数据失败");
                       }

                    }
                    
                }
               
                //$user->where("id=".$d['user_id'])->secInt("cumulative",$d['pay_amount']);
                $pro_l=M('Promote','tab_')->where(array('id'=>$d['promote_id']))->setDec("pay_limit",$d['amount']);
            }else{
                $this->record_logs("修改数据失败");
            }
            return true;
        }
        else{
            return true;
        }
    }

    /**
    *游戏返利
    */
    protected function set_ratio($data){
        $map['pay_order_number']=$data;
        $spend=M("Spend","tab_")->where($map)->find();
        $reb_map['game_id']=$spend['game_id'];
        $rebate=M("Rebate","tab_")->where($reb_map)->find();
        if($rebate['ratio']==0||null==$rebate){
            return false;
        }else{
            if($rebate['money']>0&&$rebate['status']==1){
                if($spend['pay_amount']>=$rebate['money']){
                    $this->compute($spend,$rebate);
                }else{
                    return false;
                }
            }else{
                $this->compute($spend,$rebate);
            }
        }
    }

    //计算返利
    protected function compute($spend,$rebate){
        $user_map['user_id']=$spend['user_id'];
        $user_map['game_id']=$spend['game_id'];            
        $bind_balance=$spend['pay_amount']*($rebate['ratio']/100);
        $spend['ratio']=$rebate['ratio'];
        $spend['ratio_amount']=$bind_balance;
        M("rebate_list","tab_")->add($this->add_rebate_list($spend));
        $re=M("UserPlay","tab_")->where($user_map)->setInc("bind_balance",$bind_balance);
        return $re;
    }
    /**
    *返利记录
    */
    protected function add_rebate_list($data){
        $add['pay_order_number']=$data['pay_order_number'];
        $add['game_id']=$data['game_id'];
        $add['game_name']=$data['game_name'];
        $add['user_id']=$data['user_id'];
        $add['pay_amount']=$data['pay_amount'];
        $add['ratio']=$data['ratio'];
        $add['ratio_amount']=$data['ratio_amount'];
        $add['promote_id']=$data['promote_id'];
        $add['promote_name']=$data['promote_account'];
        $add['create_time']=time();
        return $add;
    }

    /**
    *日志记录
    */
    protected function record_logs($msg=""){
        \Think\Log::record($msg);
    }
    /**
      通知CP
     */
    protected function post($param,$url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);//设置等待时间
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function FromXml($xml)
    {
        if(!$xml){
            echo "xml数据异常！";
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }
    public function getSign($params) {
        ksort($params);        //将参数数组按照参数名ASCII码从小到大排序
        foreach ($params as $key => $item) {
            if (!empty($item)) {         //剔除参数值为空的参数
                $newArr[] = $key.'='.$item;     // 整合新的参数数组
            }
        }
        $stringA = implode("&", $newArr);         //使用 & 符号连接参数
        $stringSignTemp = $stringA."&key="."b2e9b1ada984072c1f0e6cb2709b32a0";        //拼接key
        // key是在商户平台API安全里自己设置的
        $stringSignTemp = MD5($stringSignTemp);       //将字符串进行MD5加密
        $sign = strtoupper($stringSignTemp);      //将所有字符转换为大写
        return $sign;
    }

}
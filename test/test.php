<?php

/**
 * @api
 * @name    推荐位数据获取
 * @desc    这是用来获取推荐位数据的接口
 * @url     /test/test.php
 * @method  POST
 * @param   position_id   int     [必填]  推荐位ID 1:热门游戏 5:游戏幻灯图
 * @param   model_id      int     [必填]  模型ID
 * @param   game_id       int     [选填]  游戏ID
 * @result  unknow        string        难理解的返回结果字段可以添加描述
 * @result  game_desc     string        游戏描述
 */
function test () {
  return ['a' => 'wehksada', 'b' => 2131, 'c' => ['c1' => 'wqeq', 'c2' => 2131321]];
}


echo json_encode(test());
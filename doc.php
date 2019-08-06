<?php
require_once 'config.php';
require_once 'Parser.php';

//header('Content-Type:application/json');

$data = getApiDoc($config['files']);

echo json_encode([
  'errno' => 1,
  'data' => $data,
  'key' => $config['key'],
  'msg' => '操作成功',
], true);


function getApiDoc($path) {
  $parser = new Parser();
  // 获取所有需要生成api文档的文件
  $files = $parser->getFiles($path);
  // 获取所有接口注释信息
  $comments = $parser->getComments($files);
  // 将是api接口的注释信息提取并格式化
  $data = $parser->getData($comments);
  return $data;
}



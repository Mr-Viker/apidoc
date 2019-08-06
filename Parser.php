<?php
/**
 * 解析器
 */
class Parser {

  // 获取路径数组下的所有符合要求的文件
  public function getFiles($path) {
    $files = [];
    foreach ($path as $value) {
      $fileName = basename($value);
      $dir = dirname($value);
      $allFile = @scandir($dir);
      if (!$allFile) {
        die('目录不存在:' . $dir);
      }
      // 将该目录下的所有文件进行正则匹配，如果匹配成功则加入需要生成api文档的文件数组里
      foreach ($allFile as $file) {
        if (preg_match('/' . $fileName . '/', $file)) {
          $files[] = $dir . '/' . $file;
        }
      }
    }
    return $files;
  }


  // 获取文件数组下的所有注释信息
  public function getComments($files) {
    $comments = [];
    if (is_array($files)) {
      foreach ($files as $file) {
        $comments = array_merge($comments, $this->getComment($file));
      }
    } else {
      $comments = array_merge($comments, $this->getComment($files));
    }
    return $comments;
  }


  // 获取单文件的所有注释信
  protected function getComment($file) {
    $content = file_get_contents($file);
    if (preg_match_all('/\/\*(\s|.)*?\*\//', $content, $match)) {
      return $match[0] ?: [];
    }
    return [];
  }


  // 将注释数组中所有api接口的注释信息格式化
  public function getData($comments) {
    $data = [];
    foreach ($comments as $comment) {
      if (!empty($comment) && preg_match('/\* @api/', $comment)) {
        $data[] = [
          'name' => $this->getRow($comment, 'name'),
          'desc' => $this->getRow($comment, 'desc'),
          'url' => $this->getRow($comment, 'url'),
          'method' => $this->getRow($comment, 'method'),
          'params' => $this->getParams($comment),
          'results' => $this->getResults($comment),
        ];
      }
    }
    return $data;
  }


  // 获取注释块的单行内容
  protected function getRow($comment, $key) {
    if (preg_match("/\* @{$key}( *)(.*)/", $comment, $match)) {
      return array_pop($match);
    }
    return '';
  }


  // 获取注释块的参数
  protected function getParams($comment) {
    $params = [];
    if (preg_match_all('/\* @param( *)(.*)/', $comment, $match)) {
      $rawParams = array_pop($match);
      foreach ($rawParams as $param) {
        $newParam = array_merge(array_filter(explode(' ', $param)));
        $params[] = [
          'name' => $newParam[0],
          'type' => $newParam[1],
          'need' => $newParam[2],
          'desc' => $newParam[3],
        ];
      }
    }
    return $params;
  }


  // 获取注释块的返回结果
  protected function getResults($comment) {
    $results = [];
    if (preg_match_all("/\* @result( *)(.*)/", $comment, $match)) {
      $rawResults = array_pop($match);
      foreach ($rawResults as $result) {
        $newResult = array_merge(array_filter(explode(' ', $result)));
        $results[] = [
          'name' => $newResult[0],
          'type' => $newResult[1],
          'desc' => $newResult[2],
        ];
      }
      // $rawResult = array_merge(array_filter(explode(' ', array_pop($match))));
      // $results = [
      //   'type' => $rawResult[0],
      //   'desc' => $rawResult[1],
      // ];
    }
    return $results;
  }

}

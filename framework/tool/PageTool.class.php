<?php

class PageTool
{

    /**
     * 形成翻页的html代码方法
     *
     * @param $page
     * @param $pagesize
     * @param $total
     * @param $url 请求地址 index.php?p=xxx&c=Yyy&a=zzz
     * @param $params array 请求的附加参数例如array('pagesize'=>3)
     *
     * @return string 拼凑好翻页html代码！
     */
    public function show($page, $pagesize, $total, $url, $params = array())
    {
        //计算需要的信息
        $total_page = ceil($total / $pagesize);
        //处理url
        //判断$url后是否已经携带了参数
        $url_info = parse_url($url);
        if (isset($url_info['query'])) {
            $url .= '&';
        } else {
            //没有携带
            $url .= '?';
        }
        //拼凑 额外参数到url上
        foreach ($params as $key => $value) {
            $url .= $key . '=' . $value . '&';
        }
        //增加额外的参数
        $url .= 'page=';

        //拼凑翻页信息部分
        $info = <<<HTML
		总计 <span id="totalRecords">$total</span> 个记录，
		分为 <span id="totalPages">$total_page</span> 页，
		当前第 <span id="pageCurrent">$page</span> 页，
		每页 <input type="text" value="$pagesize" onblur="window.location.href='$url'+'1'+'&pagesize='+this.value" >
HTML;
        // index.php?p=back&c=Goods&a=list&pagesize=2&page=1&pagesize=3

        //链接部分
        $prev = $page == 1 ? $total_page : ($page - 1);
        $next = $page == $total_page ? 1 : ($page + 1);
        $link = <<<HTML
		 <a href="{$url}1">第一页&nbsp;&nbsp;</a>
		  <a href="{$url}{$prev}" >上一页&nbsp;&nbsp;</a>
		  <a href="{$url}{$next}">下一页&nbsp;&nbsp;</a>
		  <a href="{$url}{$total_page}">最末页</a>
HTML;
        //拼凑页码列表
        $option = <<<HTML
			<select onchange="window.location.href='$url'+this.value">
HTML;
        //opion们
        for ($p = 1; $p <= $total_page; ++$p) {
            if ($p == $page) {
                $option .= <<<HTML
					<option value="$p" selected="selected">$p</option>
HTML;
            } else {
                $option .= <<<HTML
					<option value="$p">$p</option>
HTML;
            }
        }
        $option .= '</select>';
        return $info . '<span id="page-link">' . $link . $option . '</span>';
    }
}

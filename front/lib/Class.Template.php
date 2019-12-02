<?php
class Template
{
    private $value=array();
    private $file;
    private $template_dir;

	function __construct()
	{
		$this->template_dir = INCLUDE_ROOT . 'tpl/';

	}

    /**
     * 注入单个变量
     */
    public function assign($key, $value)
    {
        $this->value[$key] = $value;
    }

    /**
     * 获取模板的位置
     * @return [type] [description]
     */
    public function path()
    {
        return $this->template_dir.$this->file;
    }

    /**
     * 展示模板
     */
    public function render($file)
    {
        $this->file = $file;
        $path = $this->path();
        if(!is_file($path))
        {
            exit('找不到对应的模板');
        }
        if($this->value){
            extract($this->value, EXTR_OVERWRITE);
        }

        ob_start();
        include $path;
        $content = ob_get_contents();
        ob_clean();
        return $content;
    }

}
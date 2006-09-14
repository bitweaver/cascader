{if $smarty.const.ACTIVE_PACKAGE == 'cascader'}
	{literal}
	<style type="text/css">
		table.calendar				{margin:1em auto; text-align:center; width:250px;}
		table.calendar a			{padding:10px; display:block:}
		table.calendar td.day		{text-align:right;}
		ul.colorpicker				{background:#eee;}
		ul.colorpicker,
		ul.colorpicker li			{border:1px solid #999;}
		ul.colorpicker,
		ul.colorpicker li,
		ul.idpicker li,
		ul.idpicker					{list-style:none; margin:0.5em; padding:0;}
		ul.colorpicker				{text-align:center;}
		ul.colorpicker li			{color:#000; font-weight:bold;}
		ul.colorpicker li span		{color:#fff;}
		ul.colorpicker li:hover		{background:#f80 !important;}
		ul.idpicker					{text-align:right; font-weight:bold;}

		/* styling the preview stuff */
		div.layout					{color:#000; font-weight:bold;}
		#preview_container			{background:#ccc; text-align:center; line-height:2em; padding:0 2em 2em 2em; border:1px solid #999;}
		#preview_header				{background:#ace;}
		#preview_wrapper			{float:left;width:100%}
		#preview_content			{background:#eca; margin:0 25%; line-height:8em;}
		#preview_navigation			{background:#aec; float:left;width:25%;margin-left:-100%}
		#preview_extra				{background:#cae; float:left;width:25%;margin-left:-25%}
		#preview_footer				{background:#cea; clear:left;width:100%}
		.preview_module				{background:#eee; margin:1em;}
	</style>
	{/literal}
{/if}

{if $gBitSystem->isFeatureActive('cascader_style')}
	<link rel="stylesheet" title="{$style}" type="text/css" href="{$gBitSystem->getConfig('cascader_style')}{$refresh}" media="screen" />
{/if}

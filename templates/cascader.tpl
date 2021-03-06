{* feel free to replace all this shoddy js *}
<script type="text/javascript">
	/* <![CDATA[ */
	{literal}
	function insertText( text ) {
		var id = getCheckedRadio($('picker').elements['rad']);
		$('text_'+id).value = text;
	}

	function getCheckedRadio( radioObj ) {
		if( !radioObj ) return "";
		var radioLength = radioObj.length;
		if( radioLength == undefined )
			if( radioObj.checked ) return radioObj.value;
			else return "";
		for( var i = 0; i < radioLength; i++ ) {
			if( radioObj[i].checked ) {
				return radioObj[i].value;
			}
		}
		return "";
	}
	{/literal}
	/* ]]> */
</script>

{strip}
<div class="admin cascader">
	<div class="header">
		<h1>{tr}Color Scheme Picker{/tr}</h1>
	</div>

	<div class="body">
		<p class="help">
			{tr}This page allows you to apply various color schemes from the <a href="http://dailycolorscheme.com/">Daily Color Scheme</a> site and view your site using those color schemes. This is <strong>not</strong> meant to create a finished theme for you, but to help you design your site. It allows you to view bitweaver with a number of different color schemes and once you have found one you like, you can design your own theme based on this scheme.{/tr}
		</p>

		{if $smarty.request.site_style_layout}
			<p class="warning">
				{tr}This setting is not stored. this is simply a way to view the site using different layout styles using your selected color scheme.{/tr}
			</p>
		{/if}

		{formfeedback hash=$feedback}

		{if $style != 'cascader'}
			{form}
				<input type="hidden" name="day" value="{$smarty.request.day}" />
				<input type="hidden" name="month" value="{$smarty.request.month}" />
				<input type="hidden" name="year" value="{$smarty.request.year}" />
				<p>You are currently not using the cascader style. If you don't get the desired effects when applying a new color style, please switch to the cascader site style.</p>
				<div class="form-group submit">
					<input type="submit" class="btn btn-default" name="cascader_style" value="Apply Cascader Style" />
				</div>
			{/form}
		{/if}

		{jstabs}
			{jstab title="Color Selector"}
				<h2>{tr}Scheme Picker{/tr}</h2>
				{$calendar}

				<h2>{tr}Generic Page Layout{/tr}</h2>
				<div id="preview_container" class="layout">
					#container
					<div id="preview_header"> #header </div>

					<div id="preview_wrapper">
						<div id="preview_content"> #content </div>
					</div>

					<div id="preview_navigation">
						#navigation
						<div class="preview_module"> .module </div>
					</div>

					<div id="preview_extra">
						#extra
						<div class="preview_module"> .module </div>
					</div>

					<div id="preview_footer"> #footer </div>
				</div>

				{if $gCascader->mInfo.colors}
					{form legend="Pick your site colors" id=picker}
						<input type="hidden" name="day" value="{$smarty.request.day}" />
						<input type="hidden" name="month" value="{$smarty.request.month}" />
						<input type="hidden" name="year" value="{$smarty.request.year}" />

						<h2>{$gCascader->mTitle}</h2>
						<p> Please select a CSS property and then apply a color to it </p>

						<table class="layout">
							<caption>{tr}Color Picker{/tr}</caption>
							<tr>
								<th style="width:50%">{tr}CSS Selector{/tr}</th>
								<th style="width:50%">{tr}Color{/tr}</th>
							</tr>
							<tr>
								<td>
									<ul class="idpicker">
										{foreach key=id from=$gCascader->mInfo.properties item=property}
											<li>
												<label>
													{$property.title|escape} <input type="radio" id="_{$id}" value="{$id}" name="rad"/>
													<input type="text" size="10" id="text_{$id}" name="cascader[{$id}]" value="{$smarty.request.cascader.$id}" />
													<a onclick="$('text_{$id}').value=''">{booticon iname="icon-trash"  ipackage=icons  iexplain="Clear"}</a>
												</label>
											</li>
										{/foreach}
									</ul>
								</td>
								<td>
									<ul class="colorpicker">
										{foreach from=$gCascader->mInfo.colors item=c}
											<li onclick="insertText('{$c}')" style="background:{$c};">{$c} <span>{$c}</span></li>
										{/foreach}
									</ul>
								</td>
							</tr>
						</table>

						<div class="form-group submit">
							<input type="submit" class="btn btn-default" name="clear_style" value="{tr}Clear Style{/tr}" />
							<input type="submit" class="btn btn-default" name="create_style" value="{tr}Create / Update Style{/tr}" />
						</div>
					{/form}
				{/if}

				{if $cssList}
					{form legend="List of already stored Schemes"}
						<input type="hidden" name="day" value="{$smarty.request.day}" />
						<input type="hidden" name="month" value="{$smarty.request.month}" />
						<input type="hidden" name="year" value="{$smarty.request.year}" />
						<p>{tr}To download a custom scheme, right click the view source icon and select: <strong>Save As</strong>{/tr}</p>

						<ul class="data">
							{foreach key=name item=file from=$cssList}
								<li class="item {cycle values="odd,even"}">
									{booticon iname="icon-ok"  ipackage="icons"  iexplain="Active Scheme"}
									&nbsp; <a href="{$smarty.const.CASCADER_PKG_URL}index.php?apply_style={$name}">{$name}</a>
									&nbsp; <a href="{$file.url}">{booticon iname="icon-search"  ipackage=icons  iexplain="View Source"}</a>
									&nbsp; <a href="{$smarty.const.CASCADER_PKG_URL}index.php?remove_style={$name}">{booticon iname="icon-trash"  ipackage=icons  iexplain="Remove File"}</a>
								</li>
							{/foreach}
						</ul>

						<div class="form-group submit">
							<input type="submit" class="btn btn-default" name="clear_all_styles" value="{tr}Remove all schemes{/tr}" />
						</div>
					{/form}
				{/if}
			{/jstab}

			{jstab title="Style Layout"}
				{legend legend="Pick Style Layout"}
					<ul id="layoutgala">
						{foreach from=$styleLayouts key=key item=layout}
							<li class="{cycle values="even,odd"}">
								<a {if $gBitSystem->getConfig('site_style_layout') == $key}class="highlight" {/if}href="{$smarty.const.CASCADER_PKG_URL}index.php?site_style_layout={$key}">
									{if $layout.gif}<img src="{$smarty.const.THEMES_PKG_URL}layouts/{$layout.gif}" alt="{tr}Layout{/tr}: {$key}" title="{tr}Layout{/tr}: {$key}"/><br />{/if}
									{if $gBitSystem->getConfig('site_style_layout') == $key}{booticon iname="icon-ok"  ipackage="icons"  iexplain="Current Style Layout"}{/if}
									{$key|replace:"_":" "}
									{if $layout.txt}<br />{include file="`$smarty.const.THEMES_PKG_PATH`layouts/`$layout.txt`"}{/if}
								</a>
							</li>
						{/foreach}
					</ul>
				{/legend}
			{/jstab}
		{/jstabs}
	</div> <!-- end .body -->
</div>  <!-- end .themes -->
{/strip}

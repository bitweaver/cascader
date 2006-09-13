<script type="text/javascript">
	/* <![CDATA[ */
	{literal}
	function insertText( text ) {
		var id = getCheckedRadio($('cascaderForm').elements['rad']);
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

<div class="admin cascader">
	<div class="header">
		<h1>{tr}Color Scheme Picker{/tr}</h1>
	</div>

	<div class="body">
		<p class="help">
			{tr}This page allows you to apply various color schemes from the <a href="http://dailycolorscheme.com/">Daily Color Scheme</a> site and view your site using those color schemes.
			This is <strong>not</strong> meant to create a finished theme for you, but to help you design your site.
			It allows you to view bitweaver with a number of different color schemes and once you have found one you like, you can design your own theme based on this scheme.{/tr}
		</p>

		{if $smarty.request.site_style_layout}
			<p class="warning">
				{tr}This setting is not stored. this is simply a way to view the site using different layout styles using your selected color scheme.{/tr}
			</p>
		{/if}

		{formfeedback hash=$feedback}

		{jstabs}
			{jstab title="Color Selector"}
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

				<p>
					Currently only 10 - 13th of september are working in the calendar
				</p>
				{$calendar}

				{if $cssList}
				<h2>{tr}List of already stored Schemes{/tr}</h2>
					<ul class="data">
						{foreach key=name item=file from=$cssList}
							<li calss="{cycle values="odd,even"}">
								{if $file.url == $gBitSystem->getConfig('cascader_style')}
									{biticon ipackage="icons" iname="dialog-ok" iexplain="Active Scheme"}
								{/if}
								<a href="{$smarty.const.CASCADER_PKG_URL}index.php?apply_style={$name}">{$name}</a>
								<a href="{$file.url}">{biticon ipackage=icons iname="edit-find" iexplain="View Source"}</a>
								<a href="{$smarty.const.CASCADER_PKG_URL}index.php?remove_style={$name}">{biticon ipackage=icons iname="edit-delete" iexplain="Remove File"}</a>
							</li>
						{/foreach}
					</ul>
				{/if}

				{if $colorScheme}
					{form id=cascaderForm}
						<input type="hidden" name="color_scheme" value="{$smarty.request.color_scheme}" />

						<h2>Color Scheme name</h2>
						<p> Please select a CSS property and then apply a color to it </p>

						<table>
							<caption>{tr}Color Picker{/tr}</caption>
							<tr>
								<th style="width:50%">{tr}CSS Selector{/tr}</th>
								<th style="width:50%">{tr}Color{/tr}</th>
							</tr>
							<tr>
								<td>
									<ul class="idpicker">
										{foreach key=id from=$cascaderIds item=css_def}
										<li>
											<label>
												{$css_def} <input type="radio" id="_{$id}" value="{$id}" name="rad"/>
												<input type="text" size="10" id="text_{$id}" name="cascader[{$id}]" value="{$smarty.request.cascader.$id}" />
											</label>
										</li>
										{/foreach}
									</ul>
								</td>
								<td>
									<ul class="colorpicker">
										{foreach from=$colorScheme item=c}
											<li onclick="insertText('{$c}')" style="background:{$c};">{$c} <span>{$c}</span></li>
										{/foreach}
									</ul>
								</td>
							</tr>
						</table>

						<div class="row submit">
							<input type="submit" name="clear_style" value="{tr}Clear Style{/tr}" />
							<input type="submit" name="create_style" value="{tr}Create Style{/tr}" />
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
									{if $gBitSystem->getConfig('site_style_layout') == $key}{biticon ipackage="icons" iname="dialog-ok" iexplain="Current Style Layout"}{/if}
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

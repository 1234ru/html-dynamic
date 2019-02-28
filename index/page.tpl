<!DOCTYPE HTML>
<html>
<head>
	<title>{*page.title*}</title>
	{%*page.css*}<link rel="stylesheet" href="{*page.css:*}">{%}
	{%*page.js*}<script src="{*page.js:*}"></script>{%}
</head>
<body>

	<div class="list">
	{%*list*}
		<div class="page-item">
			
			<a href="?page={*list:^KEY*}">
				{*list:^KEY*}. 
				<span class="number">{*list:title*}</span>
			</a>
			
			{%*list:variants*}
				<form>
					{?*list:variants:title*}<span class="variant-title">{*list:variants:title*}</span>{?}
					
					{%*list:variants:values*}
						<label>
							<input
							 type="{?*list:variants:values^COUNT > 1*}radio{?!}checkbox{?}"
							 name="v[{*list:variants:^KEY*}]" value="{*list:variants:values:^KEY*}"
							 >
							<span>{*list:variants:values:*}</span>
						</label>
					{%}
					{?*list:variants:values^COUNT > 1*}
						<label>
							<input
							 type="radio"
							 name="v[{*list:variants:^KEY*}]" value=""
							>
							<span>по умолчанию</span>
						</label>
					{?}
				</form>
			{%}
		</div>
	{%}
	</div>
</body>
</html>
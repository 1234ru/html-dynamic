<!DOCTYPE HTML>
<html>
<head>
	<title>{*page.title*}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
						<label {?!*list:variants:values^COUNT > 1*}class="single"{?}>
							<input
							 type="{?*list:variants:values^COUNT > 1*}radio{?!}checkbox{?}"
							 name="v[{*list:variants:^KEY*}]" value="{*list:variants:values:^KEY*}"
							 >
							<span>{*list:variants:values:*}</span>
						</label>
					{%}
					/*
					{?*list:variants:values^COUNT > 1*}
						<label>
							<input
							 type="radio"
							 name="v[{*list:variants:^KEY*}]" value=""
							>
							<span>по умолчанию</span>
						</label>
					{?}
					*/
				</form>
			{%}
		</div>
	{%}
	</div>
	
	<div class="github-link">
		Powered by <a href="https://github.com/1234ru/html-dynamic">https://github.com/1234ru/html-dynamic</a>
	</div>
</body>
</html>
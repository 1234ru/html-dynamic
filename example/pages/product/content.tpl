<h1>Товар такой-то</h1>

<div> 
	Цена: 
	{?!*variants.has_discount*}
		<span class="price">15000 р.</span>
	{?!}
		<span class="price-old">15000</span>
		<span class="price-discounted">12000 р.</span>
		<span class="discount">-20%</span>
	{?}
</div>

<div class="availability">
	{?!*variants.availability*}в наличии {?}
	
	{?*variants.availability="POD_ZAKAZ"*} под заказ {?}
	
	{?*variants.availability="NO"*} нет в продаже {?}
</div>
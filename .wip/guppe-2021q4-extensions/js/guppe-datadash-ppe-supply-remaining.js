jQuery(document).ready(function($) {
	const setValue = (type, parent, value) => {
		$(`.${type} .${parent}`).width(`${value}%`);
		$(`.${type} .${parent} .percentage`).html(`${value}%`);
	};
	
	// const dataUrl = 'https://storage.googleapis.com/data-dashboard-backend/1_ppe_supply_remaining';
	const dataUrl = 'https://getusppe.org/1_ppe_supply_remaining';
	const data = $.getJSON(`${dataUrl}`, (data) => {
		setValue('n95-masks', 'out-ppe', data[0].outOfPPE);
		setValue('n95-masks', 'more-7', data[0].more7Days);
		setValue('n95-masks', 'less-7', data[0].less7Days);

		setValue('nitrile-gloves', 'out-ppe', data[1].outOfPPE);
		setValue('nitrile-gloves', 'more-7', data[1].more7Days);
		setValue('nitrile-gloves', 'less-7', data[1].less7Days);
	});
});
jQuery(document).ready(function($) {
  const numberWithCommas = (x) => x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');

  const getJSONData = (url) => {
    return new Promise((resolve) => {
      $.getJSON(url, (data) => {
        resolve(data);
      });
    });
  };


  const init = async () => {
    const elMainDiv = $('div.main');
    // const dataUrl = 'https://storage.googleapis.com/data-dashboard-backend/7_request_demand_deliver';
	const dataUrl = 'https://getusppe.org/7_request_demand_deliver';
    const data = (await getJSONData(`${dataUrl}`))[0];
    
    $("#request-qty").html(numberWithCommas(data.totalRequests));
    $("#demand-qty").html(numberWithCommas(data.currentWeeklyNeed));
    $("#delivered-qty").html(numberWithCommas(data.totalDelivered));
  };

  init();

});
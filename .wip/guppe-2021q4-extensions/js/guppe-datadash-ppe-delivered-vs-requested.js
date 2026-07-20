jQuery(document).ready(function($) {
  const numberWithCommas = (x) => x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');

  const getJSONData = (url) => {
    return new Promise((resolve) => {
      $.getJSON(url, (data) => {
        resolve(data);
      });
    });
  };

  const returnHTML = (data) => {
    const percentage = (data.avgDeliveredPerWeek / data.currentWeeklyRequest) * 100;
    return `<div class="charts">
      <div class="progress">
        <div
          class="progress-bar bg-success"
          role="progressbar"
          style="width: ${percentage}%"
          aria-valuenow="${percentage}"
          aria-valuemin="0"
          aria-valuemax="100"
        ></div>
      </div>
      <div class="amounts">
        <span class="unit-amount">
          ${data.avgDeliveredPerWeek !== null ? numberWithCommas(data.avgDeliveredPerWeek) : 0}
        </span>
        <div class="description">
          <p class="text-uppercase">${data.item}</p>
          <div class="amt-fulfilled ${getDescClass(data)}">
            <b>${Math.round(percentage)}%</b> 
            fulfilled
          </div>
        </div>
        <span class="unit-amount">
          ${numberWithCommas(data.currentWeeklyRequest)}
        </span>
      </div>
    </div>`;
  };

  const init = async () => {
    const elMainDiv = $('div.main');
    //const dataUrl = 'https://storage.googleapis.com/data-dashboard-backend/6_ppe_units_delivered_vs_requested';
	const dataUrl = 'https://getusppe.org/6_ppe_units_delivered_vs_requested/';
    const data = await getJSONData(`${dataUrl}`);

    let html = '';
    for (const item of data) {data
      if (item.item == 'Nitrile Gloves') {
        item.item = 'Nitrile Gloves (pairs)'
      }
      html += returnHTML(item)
    }

    elMainDiv.append(html);
  };

  init();
});

function getDescClass (item) {
  return item.numDelivered >= item.numRequested ? 'green' : 'red'
}
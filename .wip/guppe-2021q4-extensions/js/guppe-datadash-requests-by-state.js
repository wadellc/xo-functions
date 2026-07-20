jQuery(document).ready(function($) {
	const tooltip = d3.select('div#rbs').append('div').attr('class', 'tooltip').style('opacity', 0); //#rbs
	const numberWithCommas = (x) => x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');

	const getJSONData = (url) =>
		new Promise((resolve) => {
			$.getJSON(url, (data) => {
				resolve(data);
			});
		});

	const getMapPosition = () => {
		const elWidth = $('#request-map .map').width();
		return {
			mapWidth: elWidth,
			mapHeight: elWidth * .56,
			mapScale: elWidth * .8,
			mapTranslateX: elWidth * 1.95,
			mapTranslateY: elWidth * .95,
			mapFontSize: (elWidth * .03).toString() + 'px'
		}
	}

	// Tooltop design
	const returnHTML = (data) => `
		<div class="title font-weight-bold">${data.state}</div>
		<div class="amount">
			${numberWithCommas(data.requests)}
		</div>
		<div class="top-item">
			<div class="desc">TOP ITEM REQUESTED:</div>
			<div class="item">${data.topItem}</div>
		</div>`;

	// Create Event Handlers for mouse
	const handleMouseOver = (d) => {
		tooltip.transition().duration(200).style('opacity', 1);
 		tooltip.html(returnHTML(d))
			.style('left', `${d3.event.offsetX - 70}px`)
			.style('top', `${d3.event.offsetY + 140}px`);

	};

	// Remove Tooltop when out of mouse
	const handleMouseOut = () => tooltip.transition().duration(500).style('opacity', 0);

	// Set color foreach states
	const handleSetColor = (d) => {
		if (d) {
			const amount = parseInt(d.requests, 10);
			if (amount >= 0 && amount < 200) return '#B2D2DD';
			if (amount >= 200 && amount < 400) return '#7BB8CA';
			if (amount >= 400 && amount < 600) return '#36A2B9';
			if (amount >= 600 && amount < 800) return '#128A84';
			if (amount >= 800 && amount < 1000) return '#006762';
			if (amount > 1000) return '#004440';
		}

		return '#B2D2DD';
	};

	this.displayMap = async () => {
		//const dataUrl = 'https://storage.googleapis.com/data-dashboard-backend/2_ppe_requests_by_state';
		//const polygonUrl = 'https://storage.googleapis.com/data-dashboard-backend/2_polygon.json';
		//const itemsUrl = 'https://storage.googleapis.com/data-dashboard-backend/4_most_requested_types_of_ppe';
		
		
		const dataUrl = 'https://getusppe.org/2_ppe_requests_by_state';
		const polygonUrl = 'https://getusppe.org/2_polygon/';
		const itemsUrl = 'https://getusppe.org/4_most_requested_types_of_ppe/';
		const staticAssetUri = 'https://cdn.getusppe.org/data-dashboard/assets/img/';

		const data = await getJSONData(`${dataUrl}`);
		const items = await getJSONData(`${itemsUrl}`);
		const topRequested = items['allTime'].find((el) => el.order === 1);
		$('#request-map div.amount').empty();
		$('#request-map p.top-item-name').empty();
		$('#request-map div.amount').append(numberWithCommas(data.totalRequests));
		$('#request-map p.top-item-name').append(data.topItem);
		$('#request-map div.top-item-icon img').attr('src', getImgUrl(topRequested.slug));

		const {mapWidth, mapHeight, mapScale, mapTranslateX, mapTranslateY, mapFontSize} = getMapPosition();
		const svg = d3.select('#request-map svg')
			.attr("preserveAspectRatio", "xMinYMin meet")
			.attr("viewBox", `0 0 ${mapWidth} ${mapHeight}`)

		// Map and projection
		const projection = d3
			.geoMercator()
			.scale(mapScale) // This is the zoom
			.translate([mapTranslateX, mapTranslateY]); // You have to play with these values to center your map

		// Path generator
		const path = d3.geoPath().projection(projection);

		// Load external data and boot
		const polyUrl = `${polygonUrl}`
		d3.json(polyUrl, function (polygon) {
			// Draw the map
			svg
				.append('g')
				.selectAll('path')
				.data(polygon.features)
				.enter()
				.append('path')
				.attr('fill', (d) => {
					const idx = data.states.findIndex((el) => el.code === d.stateCode);
					return handleSetColor(data.states[idx]);
				})
				.attr('d', path)
				.attr('stroke', 'white')
				.attr('stroke-width', '1.5%')
				.on('mouseover', (d) => {
					const idx = data.states.findIndex((el) => el.code === d.stateCode);
					return handleMouseOver(data.states[idx]);
				})
				.on('mouseout', (d) => {
					const idx = data.states.findIndex((el) => el.code === d.stateCode);
					return handleMouseOut(data.states[idx]);
				});

			// Add the labels
			svg
				.append('g')
				.selectAll('labels')
				.data(polygon.features)
				.enter()
				.append('text')
				.attr('x', function (d) {
					return path.centroid(d)[0];
				})
				.attr('y', function (d) {
					return path.centroid(d)[1];
				})
				.text(function (d) {
					return d.stateCode;
				})
				.attr('text-anchor', 'middle')
				.attr('alignment-baseline', 'central')
				.style('font-size', mapFontSize)
				.style('font-weight', 700)
				.style('fill', 'white')
				.style('cursor', 'pointer')
				.on('mouseover', (d) => {
					const idx = data.states.findIndex((el) => el.code === d.stateCode);
					return handleMouseOver(data.states[idx]);
				})
				.on('mouseout', (d) => {
					const idx = data.states.findIndex((el) => el.code === d.stateCode);
					return handleMouseOut(data.states[idx]);
				});
		});
	};

	this.displayMap();
});

function getImgUrl (slug) {

	//const hiResBaseRBS = 'https://storage.googleapis.com/data-dashboard-backend/img/hi-res/'
	const hiResBaseRBS = 'https://getusppe.org/';
	
	const sslug = slug === '2_way_radio' ? '_2_way_radio' : slug
	return `${hiResBaseRBS}${hiResDictionaryRBS[sslug]}-400x400`
}

const hiResDictionaryRBS = {
	n95: 'N95-resp',
	disinfecting_wipes: 'disinfect-wipes',
	surgical_mask: 'surgical-masks',
	face_shield: 'face-shields',
	gown: 'gowns-cropped',
	sanitizer: 'hand-sani', 
	nitrile_gloves: 'nitrile-gloves',
	cloth_mask: 'cloth-masks', 
	thermometer: 'thermometers', 
	disposable_booties: 'disp-booties', 
	safety_goggles: 'safety-goggles', 
	safety_glasses: 'safety-glasses',
	surgical_cap: 'surgical-caps',
	coveralls: 'coveralls',
	kn95: 'KN95-resp',
	_2_way_radio: 'two-way-comm',
	clear_mask: 'clear-masks',
	sneeze_guard: 'sneeze-guards',
	ear_saver: 'ear-savers',
	papr_shield: 'PAPR-face-shields',
	three_ply_mask: '3-ply-mask',
	handwashing_station: 'handwash-station',
	body_bag: 'body-bags'
}
jQuery(document).ready(function($) {
	const tooltip = d3.select('div#hvn').append('div').attr('class', 'tooltip-hvn').style('opacity', 0);
	const getTooltipPosition = (index, cols) => {
		const hoveredCol = $(cols[index]);
		const el = $("div#hvn")
		var parentPos = el.parent().offset();
		
		return hoveredCol.position().left - parentPos.left - $('.tooltip').first().width()/2
	}

	const returnHTML = (data) => {
		return `
		<div id="triangle-bottom"></div>
		<div class="tt-header">${data.month} ${data.year}</div>
		<div class="tt-hospitals">
			<div class="left">
				<p class="dot"></p>
				<p class="title">Hospitals</p>
			</div>
			<div class="right">${data.hospitals}%</div>
		</div>
		<div class="tt-non-hospitals">
			<ul>
				<li>
					<div class="left">
						<p class="non-dot"></p>
						<p class="non-title">OTHER FACILITIES</p>
					</div>
					<div class="non-right">${data.nonHospitals}%</div>
				</li>
				<li>
					<div class="left">
						<p class="dot"></p>
						<p class="title">SCHOOLS</p>
					</div>
					<div class="right">${data.schools}%</div>
				</li>
				<li>
					<div class="left">
						<p class="dot"></p>
						<p class="title">nursing homes</p>
					</div>
					<div class="right">${data.nursingHomes}%</div>
				</li>
				<li>
					<div class="left">
						<p class="dot"></p>
						<p class="title">small clinics</p>
					</div>
					<div class="right">${data.smallClinics}%</div>
				</li>
				<li>
					<div class="left">
						<p class="dot"></p>
						<p class="title">shelters</p>
					</div>
					<div class="right">${data.shelters}%</div>
				</li>
				<li>
					<div class="left">
						<p class="dot"></p>
						<p class="title">other</p>
					</div>
					<div class="right">${data.other}%</div>
				</li>
			</ul>
		</div>`;
	};

	const handleMouseOver = (d, i, c) => {
		console.log('d', d)
		const tooltipTop = $('#requesting-ppe-chart').position().top;
		const tooltipLeft = getTooltipPosition(i, c);
		tooltip.transition().duration(200).style('opacity', 1);
		tooltip
			.html(returnHTML(d.data))
			.style('left', `${tooltipLeft}px`)
			.style('top', `${tooltipTop}px`)
	};

	const handleMouseOut = (d, i) => {
		tooltip.transition().duration(500).style('opacity', 0);
	};

	const init = () => {
		// set the dimensions and margins of the graph
		const elDom = $('#requesting-ppe-chart');
		const margin = { top: 10, right: 20, bottom: 30, left: 40 },
			width = elDom.width() - margin.left - margin.right,
			height = elDom.width() - margin.top - margin.bottom;

		// append the svg object to the body of the page
		let svg = d3
			.select('#requesting-ppe-chart')
			.append('svg')
			.classed("svg-content-responsive", true)
			.attr("preserveAspectRatio", "xMinYMin meet")
			.attr("viewBox", `0 0 ${width + margin.left + margin.right} ${height + margin.top + margin.bottom}`)
			.append('g')
			.attr('transform', `translate(${margin.left}, ${margin.top})`);
		//const dataUrl = 'https://storage.googleapis.com/data-dashboard-backend/3_hospitals_vs_non_hospitals_requesting_ppe';
		const dataUrl = 'https://getusppe.org/3_hospitals_vs_non_hospitals_requesting_ppe';

		d3.json(`${dataUrl}`, (fetched) => {
			let data = []
			for (const key in fetched) {
				for (const item of fetched[key]) {
					item.year = key
					data.push(item)
				}
			}
			const subgroups = ['hospitals', 'nonHospitals'];
			const groups = d3
				.map(data, function (d) {
					return d.month;
				})
				.keys();

			// Add X axis
			const x = d3.scaleBand().domain(groups).range([0, width]).padding([0.2]);
			svg
				.append('g')
				.attr('transform', `translate(0, ${height})`)
				.call(d3.axisBottom(x).tickSizeOuter(0))
				.call((g) => g.selectAll('.tick text').attr('fill', '#58595b'))
				.classed('x-axis', true);

			// Add Y axis
			const y = d3.scaleLinear().domain([0, 100]).range([height, 0]);
			const yAxis = d3.axisLeft(y).ticks(10).tickFormat((d) => {
				return d + "%"
			})
			svg
				.append('g')
				.call(yAxis)
				.call((g) => g.selectAll('.tick text').attr('fill', '#58595b'))
				.classed('y-axis', true);

			// color palette = one color per subgroup
			const color = d3.scaleOrdinal().domain(subgroups).range(['#36A2B9', '#243B58']);

			//stack the data? --> stack per subgroup
			var stackedData = d3.stack().keys(subgroups)(data);

			// Show the bars
			svg
				.append('g')
				.selectAll('g')
				// Enter in the stack data = loop key per key = group per group
				.data(stackedData)
				.enter()
				.append('g')
				.attr('fill', function (d) {
					return color(d.key);
				})
				.selectAll('rect')
				// enter a second time = loop subgroup per subgroup to add all rectangles
				.data(function (d) {
					return d;
				})
				.enter()
				.append('rect')
				.attr('x', function (d) {
					return x(d.data.month);
				})
				.attr('y', function (d) {
					return y(d[1]);
				})
				.attr('height', function (d) {
					return y(d[0]) - y(d[1]);
				})
				.attr('width', x.bandwidth())
				.on('mouseover', handleMouseOver)
				.on('mouseout', handleMouseOut);
		});
	};

	init();
});
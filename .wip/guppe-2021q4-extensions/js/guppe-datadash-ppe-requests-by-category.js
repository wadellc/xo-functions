jQuery(document).ready(function($) {
	const colors = ['#bc4d34', '#7f4709', '#d77b27', '#ffc831', '#1daca4', '#128a84', '#92a5d5', '#2e6bb4', '#0c2e4d', '#660000'];

	const fetchData = (element, data, items) => {
		let dates = [];

		items.map((item) => (item.sum = 0));

		data.map((dataItem) => {
			if (dataItem.data) {
				Object.keys(dataItem.data).map((year) => {
					Object.keys(dataItem.data[year]).map((month) => {
						if (dates.map((d) => `${d.year} ${d.month}`).indexOf(`${year} ${month}`) > -1) return;

						dates.push({
							label: `${month.toUpperCase()}`,
							fullLabel: `${month.toUpperCase()} ${year}`,
							year: year,
							month: month,
						});
					});
				});
			}
		});

		dates.sort((a, b) => {
			const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
			return a.year !== b.year
				? a.year > b.year
					? 1
					: -1
				: months.indexOf(a.month.toUpperCase()) - months.indexOf(b.month.toUpperCase());
		});

		$(`#${element} .label-since`).html(`SINCE ${dates[0].month.toUpperCase()} ${dates[0].year}`);

		return dates.map((d) => {
			let tmp = d;
			let sum = 0;

			data.map((data, index) => {
				if (data.data && data.data[d.year] && data.data[d.year][d.month]) {
					items[index].sum += data.data[d.year][d.month];
					tmp[`item-${data.id}`] = data.data[d.year][d.month];
					tmp[`item-${data.id}-label`] = items[index].name;
					sum += data.data[d.year][d.month];
					
				} else {
					tmp[`item-${data.id}`] = 0;
				}
			});

			data.map((data) => {
				if (data.data && data.data[d.year] && data.data[d.year][d.month]) {
					tmp[`item-${data.id}-percent`] = ((data.data[d.year][d.month] / sum) * 100).toFixed();
				} else {
					tmp[`item-${data.id}-percent`] = 0;
				}
			});

			return tmp;
		});
	};

	const initChart = async (element, data, items) => {
		var chart;

		// init chart
		chart = am4core.create(`${element}-chart`, am4charts.XYChart);
		chart.data = fetchData(element, data, items);

		/* Create axes */
		var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
		categoryAxis.cursorTooltipEnabled = false;
		categoryAxis.dataFields.category = 'label';
		categoryAxis.renderer.labels.template.fill = am4core.color('#58595B');
		categoryAxis.renderer.labels.template.fontSize = 16;
		categoryAxis.renderer.labels.template.fontWeight = 700;
		categoryAxis.renderer.minGridDistance = 30;

		/* Create value axis */
		var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
		valueAxis.renderer.grid.template.strokeDasharray = '4 4';
		valueAxis.renderer.labels.template.dy = 10;
		valueAxis.renderer.labels.template.fill = am4core.color('#58595B');
		valueAxis.renderer.labels.template.fontSize = 16;

		var tooltipHTML = `		
		<div style="top: 0; left: 0; margin: 5px; z-index: 1060; display: block; max-width: 290px; font-style: normal; font-weight: 400; font-family: 'Open Sans', sans-serif; line-height: 1.5; text-align: left; text-align: start;  text-decoration: none; text-shadow: none; text-transform: none; letter-spacing: normal; word-break: normal; word-spacing: normal; white-space: normal; line-break: auto; font-size: .875rem; word-wrap: break-word; background-color: #fff; background-clip: padding-box; border: 1px solid rgba(0,0,0,.2); border-radius: .3rem;box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.4); border: #a7a9ac 1px solid; border-radius: 0;">
			<div id="triangle-bottom"></div>
			<div style="background: #6d6e71 !important; text-align: center; padding: 5px; border-radius: 0;">
				<h3 style="font-size: 16px; text-transform: uppercase; color: #fff; margin: 0;">{fullLabel} REQUESTS</h3>
			</div>
			<div style="padding: .5rem .75rem; color: #212529;">
				<table>`;

		var indexColor = 0;
		items.map((item) => {
			tooltipHTML += `<tr class='chart-legend-row' style='line-height:15px'>
				<td class='color-cell' style="padding: 1px 3px; margin: 0; vertical-align: top; border: none;">
					<p class='color-circle' style="margin: 0; width: 10px; height: 10px;	background-color: ${am4core.color(
				colors[indexColor++],
			)}; border-radius: 20px;">
					</p>
				</td>
				<td style="padding: 1px 3px; margin: 0; vertical-align: middle; border: none; width: 60%;">
					<p style="margin: 0;text-transform: uppercase; font-size: 11px; color: #58595b;">
						${item.name}
					</p>
				</td>
				<td style="padding: 1px 3px; margin: 0; vertical-align: middle; border: none; width: 20%; text-align: right;">
					<strong>{item-${item.id}}</strong>
				</td>
				<td style="padding: 1px 3px; margin: 0; vertical-align: middle; border: none; width: 20%; text-align: right;">
					{item-${item.id}-percent}%
				</td>
			</tr>`;
		});

		tooltipHTML += `</table>
			</div>
		</div>`;

		items.map((item, index) => {
			/* Create series */
			var series = chart.series.push(new am4charts.LineSeries());
			series.dataFields.valueY = `item-${item.id}`;
			series.dataFields.categoryX = 'label';
			series.name = item.name;
			series.strokeWidth = 5;
			series.stroke = am4core.color(colors[index]);
			series.tooltip.background.opacity = 0;
			series.tooltip.pointerOrientation = 'vertical';
			series.tooltipHTML = tooltipHTML;
		});

		/* Create a cursor */
		chart.cursor = new am4charts.XYCursor();
		chart.cursor.maxTooltipDistance = -1;

		/* Zoom out Button */
		chart.zoomOutButton.background.fill = am4core.color('#939598');
		chart.zoomOutButton.background.states.getKey('hover').properties.fill = am4core.color('#0C2E4D');
		chart.zoomOutButton.background.states.getKey('down').properties.fill = am4core.color('#36A2B9');

		// init legend
		items.map((item, index) => {
			$(`#${element} .legends`).append(`
				<li>
					<style>
					#customCheckLabel${item.id}:before {
						background: ${chart.series.getIndex(index).stroke.hex} !important;
						box-shadow: none;
						height: 20px;
						width: 20px;
					}
					#customCheckLabel${item.id}:after {
						height: 20px;
						width: 20px;
					}
					</style>
					<div class="custom-control custom-checkbox float-left">
						<input 
							type="checkbox" 
							class="custom-control-input" 
							id="customCheck${item.id}" 
							data-index="${index}" checked 
						/>
						<label 
							class="custom-control-label" 
							for="customCheck${item.id}" 
							id="customCheckLabel${item.id}"
						>
							${item.name} &nbsp; 
						</label>
						<div style="display: block;float: right; position:relative;"
						>
							<i class="fa fa-info-circle" onclick="onDetails('${item.id}')"></i>
							<div id="details-${item.id}" class='details-tooltip display-none' style="">
								<div style="background: #6d6e71 !important; text-align: center; padding: 5px; border-radius: 0;">
									<h3 style="font-size: 16px; text-transform: uppercase; color: #fff; margin: 0;">${item.name}</h3>
								</div>
								<div style="padding: .5rem .75rem; color: #212529;">
									<div class='row'>
										<div class='col-md-8'>
											<p class='details-content'>${item.description}
											</p>
										</div>
										<div class='col-md-4'>
											<img class='ppe-icon' src='${item.icon}'>
										</div>
									</div>
								</div>
							</div>
						</div>
						
					</div>
					<small class="text-capitalize float-right">
						${Number(item.sum).toLocaleString()}
					</small>
					<div class="clearfix"></div>
				</li>
			`);

			$(`#${element} .custom-control-input`).on('click', function (e) {
				let index = $(this).data('index');

				if (index === undefined) {
					return;
				}

				if (e.target.checked) {
					chart.series.getIndex(index).show();
				} else {
					chart.series.getIndex(index).hide();
				}
			});

			$('[data-toggle="popover"]').popover({
				html: true,
			});
		});

		setInterval(() => chart.logo.hide(), 1000);
	};

	const init = async () => {
		// const dataUrl = 'https://storage.googleapis.com/data-dashboard-backend/5_requests_for_ppe_category';
		const dataUrl = 'https://getusppe.org/5_requests_for_ppe_category/'

		// import data
		const data = await $.getJSON(`${dataUrl}`);
		let container1 = {
			data: [],
			items: [],
		};
		let container2 = {
			data: [],
			items: [],
		};
		let container3 = {
			data: [],
			items: [],
		};

		const container1codes = ['n95','kn95','surgical_mask','cloth_mask', 'three_ply_mask', 'clear_mask','face_shield','papr_shield','safety_goggles','safety_glasses']
		const container2codes = ['nitrile_gloves','gown','surgical_cap','coveralls','disposable_booties']
		const container3codes = ['sanitizer', 'disinfecting_wipes','sneeze_guard','handwashing_station','thermometer', 'ear_saver', '2_way_radio', 'body_bag']



		// set properties of items
		let id = 1
		for (const key in data) {
			const item = data[key]
			const descKey = key=='2_way_radio'?'_2_way_radio':key
			item.id = id
			item.label = {
				id: item.id,
				name: item.item,
				description: ppeDescriptions[descKey] && ppeDescriptions[descKey].description,
				icon: ppeDescriptions[descKey] && getImgUrl(ppeDescriptions[descKey].slug)
			}
			delete item.item
			id ++
		}

		// put data into graph containers
		for(const key of container1codes) {
			const item = data[key]
			container1.data.push(item)
			container1.items.push(item.label)
		}

		for(const key of container2codes) {
			const item = data[key]
			container2.data.push(item)
			container2.items.push(item.label)
		}

		for(const key of container3codes) {
			const item = data[key]
			container3.data.push(item)
			container3.items.push(item.label)
		}

		initChart('container1', container1.data, container1.items);
		initChart('container2', container2.data, container2.items);
		initChart('container3', container3.data, container3.items);
	};

	init();
});

var currentDetail = '';

function onDetails(id) {
	if (currentDetail && currentDetail.length > 0) {
		jQuery(currentDetail).addClass('display-none');
	}

	currentDetail = '#details-' + id;
	jQuery('#details-' + id).removeClass('display-none');

	setTimeout(() => {
		jQuery('#details-' + id).addClass('display-none');
	}, 5000);
}



const ppeDescriptions = {
	n95: {
		description: 'The N95 mask is a specialized medical-grade mask that filters out 95% of very small particles and pathogens in the air when worn correctly. This is an especially important PPE type used to protect health care professionals when working closely with patients who have airborne illnesses in aerosolizing environments. N95 masks are designed to provide a flush seal against the skin without any gaps, to provide utmost protection.', 
		slug: 'n95'
	},
	kn95: {
		description: 'KN95 masks filter viral particles to prevent the transmission and contraction of a virus. KN95 respirators differ from N95 respirators because they are not regulated by U.S. agencies. While KN95s are not recommended by NIOSH as a substitution for N95s when utilized by health care professionals; they can be utilized in non-medical environments as a form of adequate protection.',
		slug: 'kn95'
	},
	surgical_mask: {
		description: 'Surgical masks are a form of medical-grade PPE that provides the wearer protection from droplet-based viral particles. Surgical masks are disposable and are for single-use only.',
		slug: 'surgical_mask'
	},
	cloth_mask: {
		description: 'Cloth masks are the recommended form of PPE for general use while engaging in activies outside the home and around others to prevent the spread of COVID and reduce risk of contracting COVID.',
		slug: 'cloth_mask'
	},
	three_ply_mask: {
		description: '3-ply masks are made of 3 layers and can be secured to the face by ear loops, elastic straps, or head ties. 3-ply masks provide adequate protection against droplet viral particles.',
		slug: 'three_ply_mask'
	},
	clear_mask: {
		description: 'Clear masks can be worn by providers to enchance communication with people who have communication disorders.',
		slug: 'clear_mask'
	},
	face_shield: {
		description: 'Face shields are made of a clear, plastic material to allow the wearer to see. These shields can cover either half of the wearer\'s face, including eyes and nose, or the wearer\'s entire face. Face shields protect the wearer from tramission of the virus through their eyes and mucous membranes.',
		slug: 'face_shield'
	},
	papr_shield: {
		description: 'PAPR Shields are an alternative form of PPE that can be utilized when providing direct care to people with COVID if N95 masks and face sheilds are unvailable, or, if the wearer cannot obtain a flush seal from an N95 due to incorrect sizing of N95 mask or facial hair. PAPRs include a full facepeice and hood/helmet and air purifying cartridge to filter air.',
		slug: 'papr_shield'
	},
	safety_goggles: {
		description: 'Safety goggles can be worn to protect eyes from the transmission of virus particles. Safety goggles provide a flush seal against the wearer\'s face, providing greater protection than safety glasses.',
		slug: 'safety_goggles'
	},
	safety_glasses: {
		description: 'Safety glasses can be worn to protect eyes from the transmission of virus particles. Safety glasses may or may not provide a flush seal against the wearer\'s face, affecting the risk for transmission.',
		slug: 'safety_glasses'
	},
	nitrile_gloves: {
		description: 'Nitrile gloves provide hand protection when touchiing surface and providing care to prevent the spread and trasmision of virus particles. NItrile gloves should be properaly disposed of after single-use, to prevent cross-contamination and spread amongst people and other items and surfaces.',
		slug: 'nitrile_gloves'
	},
	gown: {
		description: 'Gowns are a form of medical PPE worn over clothing to reduce the spread of airborne and droplet particles of the COVID virus and protect the wearer from contracting or transmitting the virus to others. While not as effective as coveralls, they provide an additional layer of protection to the user and can be used as a safe alternative. Gowns should cover the wearer\'s body completely, and multiple different sizes may be needed to appropriately protect staff members.',
		slug: 'gown'
	},
	surgical_cap: {
		description: 'Surgical caps cover hair to reduce the tramission of airborne and droplet virus particles to the wearer. Surgical caps can be made of fabric that is washable, or can be made of single-use materials and disposable.',
		slug: 'surgical_cap'
	},
	coveralls: {
		description: 'Coveralls are a form of medical PPE worn over clothing to reduce the spread of airborne and droplet particles of the COVID virus and protect the wearer from contracting or transmitting the virus to others.',
		slug: 'coveralls'
	},
	disposable_booties: {
		description: 'Booties are a form of PPE worn over shoes to minimize transmission of virus particles from floors to shoes. Fluids/sputum from virus particles on hospital/care facility floors can be transmitted to shoes. Booties can prevent spread of virus particles to other areas by covering shoes and being properly disposed of after leaving a room.',
		slug: 'disposable_booties'
	},
	sanitizer: {
		description: 'Hand Sanitizer is used to clean hands before, during, and after providing care and after touching high volume surfaces/items. Hand sanitizer helps reduce the transmission and contraction of the virus. However, if hands are visibly soiled, soap and hot water are recommended to thouroughly clean hands.',
		slug: 'sanitizer'
	},
	disinfecting_wipes: {
		description: 'Disinfecting wipes are utilized to clean equipment, computers, and other items utilized to reduce spread of the virus amongst people.',
		slug: 'disinfecting_wipes'
	},
	sneeze_guard: {
		description: 'Sneeze guards are clear plexiglass/plastic partitions that can be used in small or crowded spaces, like offices, schools, and restaurants to reduce transmission of airborne and droplet particles between people.',
		slug: 'sneeze_guard'
	},
	handwashing_station: {
		description: 'Handwash stations allow the user to safely wash their hands with soap and water to reduce the risk for transmission of virus particles and contraction of the virus.',
		slug: 'handwashing_station'
	},
	thermometer: {
		description: 'Thermometers are utilized by individuals, schools, offices, healthcare facilties, and businesses to assess for fever and possible infection. Thermometers can be infrared, ear, or digital. If ear or digital thermometers are being used to assess temperature amongst many people, plastic probe covers should also be purchased and utilized to prevent spread of possible viral particles. All thermometers shoulder be cleaned after each individual use.',
		slug: 'thermometer'
	},
	ear_saver: {
		description: 'Ear savers reduce discomfort experienced while wearing masks, including N95 and surgical masks, for prolonged periods of time. They also reduce skin breakdown and bruising around the ears. There are many different types of ear savers that can assist in reducing this discomfort from wearing masks for prolonged periods of time.',
		slug: 'ear_saver'
	},
	_2_way_radio: {
		description: 'Radios that can be used to communicate with someone inside a COVID isolation room without entering the room.',
		slug: '_2_way_radio'
	},
	body_bag: {
		description: 'Body bags are a safe, protective, non-porous bag or container designed to contain a human body for storage and transportation after expiration.',
		slug: 'body_bag'
	}
}


function getImgUrl (slug) {

	//const hiResBaseRBS = 'https://storage.googleapis.com/data-dashboard-backend/img/hi-res/'
	const hiResBaseRBS = 'https://getusppe.org/';
	
	const sslug = slug === '2_way_radio' ? '_2_way_radio' : slug
	return `${hiResBaseRBS}${hiResDictionaryRBS[sslug]}-400x400`
}

const hiResDictionaryRBC = {
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
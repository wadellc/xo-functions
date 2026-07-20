jQuery(document).ready(function($) {
	const getJSONData = (url) => {
		return new Promise((resolve) => {
			$.getJSON(url, (data) => {
				resolve(data);
			});
		});
	};

	const returnHTML = (data) => {
		return `
			<div class="item">
				<div class="number">1</div>
				<img 
					src="${getImgUrl(data[0].slug)}" 
					alt="" 
					width="80" 
					height="80" 
				/>
				<div class="label">${data[0].item}</div>
			</div>
			<div class="divider"></div>
			<div class="item">
				<div class="number">2</div>
				<img 
					src="${getImgUrl(data[1].slug)}" 
					alt="" 
					width="80" 
					height="80" 
				/>
				<div class="label">${data[1].item}</div>
			</div>
			<div class="divider"></div>
			<div class="item">
				<div class="number">3</div>
				<img 
					src="${getImgUrl(data[2].slug)}" 
					alt="" 
					width="80" 
					height="80" 
				/>
				<div class="label">${data[2].item}</div>
			</div>
			<div class="divider"></div>
			<div class="item">
				<div class="number">4</div>
				<img 
					src="${getImgUrl(data[3].slug)}" 
					alt="" 
					width="80" 
					height="80" 
				/>
				<div class="label">${data[3].item}</div>
			</div>`;
	};

	this.displayData = async (type) => {
		// const dataUrl = 'https://storage.googleapis.com/data-dashboard-backend/4_most_requested_types_of_ppe';
		
		const dataUrl = 'https://getusppe.org/4_most_requested_types_of_ppe';
		const data = await getJSONData(`${dataUrl}`);

		const elImages = $('div.images.mrq');
		if (type === 'allTime') {
			elImages.empty();
			$('button.right').removeClass('active');
			$('button.left').addClass('active');
			const html = returnHTML(data['allTime']);
			elImages.append(html);
		} else {
			elImages.empty();
			$('button.left').removeClass('active');
			$('button.right').addClass('active');
			const html = returnHTML(data['june2021']);
			elImages.append(html);
		}
	};

	$('button.left').focus();
	this.displayData('allTime');
});
function getImgUrl (slug) {

	//const hisResBaseRBS = 'https://storage.googleapis.com/data-dashboard-backend/img/hi-res/'
	const hiResBaseRBS = 'https://getusppe.org/';
	
	const sslug = slug === '2_way_radio' ? '_2_way_radio' : slug
	return `${hisResBaseRBS}${hiResDictionaryRBS[sslug]}-400x400`
}

const hiResDictionaryMRQ = {
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
import { api_endpoint } from "./api_endpoint.js";

console.log(`API_ENDPOINT === ${api_endpoint}`);

function isId(value) {
	const t = typeof value;
	return t === 'string' || t === 'number';
}

// Funktion zum Abrufen aller Exponate (wird verwendet, wenn kein Suchbegriff eingegeben wird)
function fetchArbitraryExhibits(page_number, count_per_page) {
	let url = api_endpoint + '/exhibits';
	if (typeof page_number === 'number') {
		url += '?page_number=' + page_number.toString();
		if (typeof count_per_page === 'number') {
			url += '&count_per_page=' + count_per_page.toString();
		}
	} else if (typeof count_per_page === 'number') {
		url += '?count_per_page=' + count_per_page.toString();
	}
	
	console.log(`fetchExhibits(): url === ${url}`);
	return fetch(url)
		.then(response => {
			if (!response.ok) {
				throw new Error('Fehler beim Abrufen der API');
			}
			return response.json();
		})
		.then(data => {
			// Hole die ID aus der URL
			console.log(`fetchExhibits(): data ===`);
			console.log(data);
			return data;
			// renderMain(data); // Zeigt die Exponate an
			// renderExhibitSlider(data);
			// renderListTeaser(data);
			// renderList(data);
			
			// const params = new URLSearchParams(window.location.search);
			// const exhibit_id = params.get('id');
			// console.log(`Auf Detailseite: exhibit_id === ${exhibit_id}`);
			// if (typeof exhibit_id === 'number' || typeof exhibit_id === 'string') {
			// 	renderDetail(exhibit_id); // Zeigt einzelne Exponate an
			// }
		})
		.catch(error => {
			console.error('Fehler beim Abrufen der Daten:', error);
		});
}

function fetchSpecificExhibit(exhibit_id) {
	if (!isId(exhibit_id)) {
		throw new Error(`fetchSpecificExhibit(): invalid exhibit_id === ${exhibit_id}`);
	}
	return fetch(api_endpoint + `/exhibit/${exhibit_id}`)
		.then(response => {
			if (!response.ok) {
				throw new Error('Fehler beim Abrufen der API');
			}
			return response.json();
		})
		.then(data => {
			data.id = exhibit_id; // praktisch
			console.log(`fetchSpecificExhibit(): data ===`);
			console.log(data);
			return data;
		})
		.catch(error => {
			console.error('Fehler:', error);
		});
}

function getThumbnailUrl(exhibitData) {
	const exhibitImages = exhibitData.images; // ID des ersten Bildes
	if (exhibitImages.length > 0) {
		const imageID = exhibitImages[0].id;
		return api_endpoint + `/thumbnail/${imageID}`;
	}
	return 'assets/images/placeholder.jpg';
}

function getImageUrl(exhibitData) {
	const exhibitImages = exhibitData.images; // ID des ersten Bildes
	if (exhibitImages.length > 0) {
		const imageID = exhibitImages[0].id;
		return api_endpoint + `/image/${imageID}`;
	}
	return 'assets/images/placeholder.jpg';
}

async function renderMain() {
	const exhibits = await fetchArbitraryExhibits(0, 4);
	
	// Hole nur das erste Exponat aus dem Array
	const firstExhibit = exhibits[0];

	const firstExhibitData = await fetchSpecificExhibit(firstExhibit.id);
	const firstImageUrl = getImageUrl(firstExhibitData); // URL zum Thumbnail-Bild

	const container = document.getElementById("renderMain");
	// Left Content Exponat
	container.innerHTML = `
		<div class="row">
			<div class="col-lg-6">
				<div class="left-content" style="">
					<div class="thumb">
						<img src="${firstImageUrl}" alt="Main Image" class="img-fluid" style="aspect-ratio: 3 / 2; object-fit: cover;">
						<div class="inner-content">
							<h4>${firstExhibitData.name}</h4>
							<span>${firstExhibitData.short_description}</span>
							<div class="main-border-button">
								<a href="./single-product.html?id=${firstExhibitData.id}">Mehr Details zum Exponat!</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div id="mainRenderSubDiv" class="row"></div>
			</div>
		</div>`;

	// Hole die restlichen drei Exponate (falls vorhanden)
	const remainingExhibits = exhibits.slice(1, 4); // Die nächsten 3 Exponate (Index 1 bis 3)
	for (const exhibit of remainingExhibits) {
		const exhibitData = await fetchSpecificExhibit(exhibit.id);
		const imageUrl = getImageUrl(exhibitData);
		const container = document.getElementById("mainRenderSubDiv");
		container.innerHTML += `
			<div class="right-content col-lg-6">
				<div class="right-first-image">
					<div class="thumb">
						<div class="inner-content">
							<h4>${exhibitData.name}</h4>
							<span>${exhibitData.manufacturer}</span>
						</div>
						<div class="hover-content">
							<div class="inner">
								<h4>${exhibitData.name}</h4>
								<p>${exhibitData.short_description}</p>
								<div class="main-border-button">
									<a href="./single-product.html?id=${exhibitData.id}">Exponat Info</a>
								</div>
							</div>
						</div>
						<img src="${imageUrl}" alt="Main Image" class="img-fluid">
					</div>
				</div>
			</div>
		`;
	}
}

async function renderExhibitSlider() {
	const exhibits = await fetchArbitraryExhibits(0, 9);
	
	if (exhibits.length < 9) {
		throw new Error('Nicht genügend Exponate für den Slider vorhanden. Mindestens 9 Exponate erforderlich.');
	}
	
	const container = document.getElementById("renderExhibitSlider");
	let slidesHTML = "";
	
	// Iteriere durch die Exponate in Schritten von 3, um Slides zu erstellen
	for (let i = 0; i < 9; i += 3) {
		const currentExhibits = exhibits.slice(i, i + 3); // Hole 3 Exponate
		let slideContent = "";

		for (const exhibit of currentExhibits) {
			const exhibitData = await fetchSpecificExhibit(exhibit.id);
			const thumbnailUrl = getThumbnailUrl(exhibitData); // URL zum Thumbnail-Bild
			
			slideContent += `
				<div class="col-3 exhibit-item me-3">
					<img src="${thumbnailUrl}" class="d-block w-100" alt="Exhibit Image">
					<div class="mb-5 text-center d-none d-md-block">
						<h5>${exhibitData.name}</h5>
						<p>${exhibitData.short_description}</p>
					</div>
				</div>`;
		}

		slidesHTML += `
			<div class="carousel-item ${i === 0 ? 'active' : ''}">
				<div class="row justify-content-center">
					${slideContent}
				</div>
			</div>`;
	}

	// Render den Slider mit den generierten Slides
	container.innerHTML = `
		<div class="container">
			<div class="row">
				<div class="col-lg-6">
					<div class="section-heading">
						<h2>Unsere Exponate</h2>
						<span>Einige unserer Exponate im Überblick!</span>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div id="carouselExampleCaptions" class="carousel slide">
						<div class="carousel-indicators">
							<button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
							<button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
							<button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
						</div>
						<div class="carousel-inner">
							${slidesHTML}
						</div>
						<button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
							<svg xmlns="http://www.w3.org/2000/svg" height="25" width="18.5" viewBox="0 0 320 512">
								<path d="M34.5 239L228.9 44.7c9.4-9.4 24.6-9.4 33.9 0l22.7 22.7c9.4 9.4 9.4 24.5 0 33.9L131.5 256l154 154.8c9.3 9.4 9.3 24.5 0 33.9l-22.7 22.7c-9.4 9.4-24.6 9.4-33.9 0L34.5 273c-9.4-9.4-9.4-24.6 0-33.9z"/>
							</svg>
							<span class="visually-hidden">Previous</span>
						</button>
						<button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Next</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	`;
}

async function renderListTeaser() {
	const container = document.getElementById("renderListTeaser");
	container.innerHTML += `
		<div class="container">
			<div class="row">
				<div class="col-lg-6">
					<div class="left-content">
						<h2>Erkunde unsere Exponate</h2>
						<span>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore .</span>
						<div class="quote">
							<i class="fa fa-quote-left"></i><p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed.</p>
						</div>
						<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur.</p>
						<p>At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum.</p>
						<div class="main-border-button">
							<a href="products.html">Alle Exponate</a>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="right-content">
						<div class="row" id="exhibitRow">
							<!-- Exponate werden hier dynamisch eingefügt -->
						</div>
					</div>
				</div>
			</div>
		</div>`;
	
	// Dynamisch 4 Exponate rendern
	const exhibitRow = document.getElementById("exhibitRow");
	const numberOfExhibits = 4; // Anzahl der gewünschten Exponate
	
	const exhibits = await fetchArbitraryExhibits(0, numberOfExhibits);
	
	for (const exhibit of exhibits) {
		const exhibitData = await fetchSpecificExhibit(exhibit.id);
		const thumbnailUrl = getThumbnailUrl(exhibitData);
	
		exhibitRow.innerHTML += `
			<div class="col-lg-6">
				<div class="exhibit-item">
					<div class="thumb" style>
						<img src="${thumbnailUrl}" alt="" style="aspect-ratio: 1 / 1; object-fit: cover;">
						<div class="hover-content">
							<h4>${exhibitData.name}</h4>
							<p>${exhibitData.short_description}</p>
							<div class="main-border-button">
								<a href="./single-product.html?id=${exhibitData.id}">More Info</a>
							</div>
						</div>
					</div>
				</div>
			</div>`;
	}
}

async function renderList() {
	const allExhibits = await fetchArbitraryExhibits(0, 99999);
	const container = document.getElementById("renderList");
	for (const exhibit of allExhibits) {
		const exhibitData = await fetchSpecificExhibit(exhibit.id);
		const thumbnailUrl = getThumbnailUrl(exhibitData);
		
		container.innerHTML += `
		<div class="col-lg-4">
			<div class="item">
				<div class="right-first-image">
					<div class="thumb">
						<div class="inner-content position-absolute text-center text-white" style="top: 35%; left: 10%;">
							<h4>${exhibitData.name}</h4>
							<span>${exhibitData.short_description}</span>
						</div>
						<div class="hover-content">
							<div class="inner">
								<h4>${exhibitData.name}</h4>
								<p>${exhibitData.short_description}</p>
								<div class="main-border-button">
									<a href="./single-product.html?id=${exhibitData.id}">Exponat Info</a>
								</div>
							</div>
						</div>
						<img src="${thumbnailUrl}">
					</div>
				</div>
			</div>
		</div>`;
	}
}

async function renderDetail() {
	const params = new URLSearchParams(window.location.search);
	const exhibit_id = params.get('id');
	console.log(`Auf Detailseite: exhibit_id === ${exhibit_id}`);
	
	const container = document.getElementById("renderDetail");
	// Hole nur das erste Exponat aus dem Array
	const exhibitData = await fetchSpecificExhibit(exhibit_id)
	
	const imageUrl = getImageUrl(exhibitData);
	
	// Left Content Exponat
	container.innerHTML = `
		<div style="width: 80%; margin-inline-start: auto; margin-inline-end: auto; display: block;">
			<div style="width: 49%; height: 40rem; display: inline-block;">
				<img src="${imageUrl}" alt="Main Image" style="width: 100%; height: 100%; object-fit: contain;">
			</div>
			<div style="width: 49%; display: inline-block;">
				<div class="inner-content">
					<h4>${exhibitData.name}</h4>
					<span>${exhibitData.short_description}</span>
				</div>
			</div>
		</div>`;
}

function selectRender() {
	const _renderDetail = document.getElementById("renderDetail");
	if (_renderDetail) {
		renderDetail();
	}
	const _renderMain = document.getElementById("renderMain");
	if (_renderMain) {
		renderMain();
	}
	const _renderExhibitSlider = document.getElementById("renderExhibitSlider");
	if (_renderExhibitSlider) {
		renderExhibitSlider();
	}
	const _renderListTeaser = document.getElementById("renderListTeaser");
	if (_renderListTeaser) {
		renderListTeaser();
	}
	const _renderList = document.getElementById("renderList");
	if (_renderList) {
		renderList();
	}
}

// Initiales Abrufen aller Exponate
// fetchExhibits();

addEventListener("DOMContentLoaded", (event) => {
	selectRender();
});

import componentLoader from './components/componentLoader';
import mainMenu from './components/mainMenu';
import momentSlider from './components/momentSlider';
import productSlider from './components/productSlider';
import alliesSlider from './components/alliesSlider';
import storeLocator from './components/storeLocator';
import waysSlider from './components/waysSlider';
import believeCards from './components/believeCards';
import reviewsSlider from './components/reviewsSlider';
import guideSlider from './components/guideSlider';
import ingredientsSlider from './components/ingredientsSlider';
import blogSlider from './components/blogSlider';
import blogFilter from './components/blogFilter';
import productShowcase from './components/productShowcase';
import mushroomsSplit from './components/mushroomsSplit';
import mainSlider from './components/mainSlider';
import mainBanner from './components/mainBanner';
import foundersCards from './components/foundersCards';
import communitySplit from './components/communitySplit';
import communityBanner from './components/communityBanner';
import newsletterBanner from './components/newsletterBanner';
import contactForm from './components/contactForm';

const initEagerComponents = () => {
	mainMenu();
	mainSlider();
	mainBanner();
	productShowcase();
	blogFilter();
	foundersCards();
	communitySplit();
	communityBanner();
	newsletterBanner();
	contactForm();

	if (
		typeof window.Prism !== 'undefined' &&
		document.querySelector('code[class*="language-"], [class*="language-"] code')
	) {
		window.Prism.highlightAll();
	}
};

const initComponents = () => {
	document.documentElement.classList.remove('no-js');
	document.documentElement.classList.add('js');
	document.documentElement.dataset.componentsReady = 'true';

	initEagerComponents();
	componentLoader([
		{ selector: '.moment-slider', init: momentSlider },
		{ selector: '.product-slider', init: productSlider },
		{ selector: '.mushrooms-split', init: mushroomsSplit },
		{ selector: '.allies-slider', init: alliesSlider },
		{ selector: '.store-locator', init: storeLocator },
		{ selector: '.ways-slider', init: waysSlider },
		{ selector: '.believe-cards', init: believeCards },
		{ selector: '.reviews-slider', init: reviewsSlider },
		{ selector: '.guide-slider', init: guideSlider },
		{ selector: '.ingredients-slider', init: ingredientsSlider },
		{ selector: '.blog-slider', init: blogSlider },
	]);
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initComponents, { once: true });
} else {
	initComponents();
}

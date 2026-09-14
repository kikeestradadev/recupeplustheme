;
import { initWhenVisible } from './components/initWhenVisible';
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

const lazyInit = (selector, init) => {
	document.querySelectorAll(selector).forEach((el) => {
		initWhenVisible(el, () => init(el));
	});
};

const initComponents = () => {
	mainMenu();
	mainBanner();

	lazyInit('[data-main-slider], .main-slider', mainSlider);
	lazyInit('.moment-slider', momentSlider);
	lazyInit('.product-slider', productSlider);
	lazyInit('.allies-slider', alliesSlider);
	lazyInit('.ways-slider', waysSlider);
	lazyInit('.reviews-slider', reviewsSlider);
	lazyInit('.guide-slider', guideSlider);
	lazyInit('.ingredients-slider', ingredientsSlider);
	lazyInit('.blog-slider', blogSlider);

	productShowcase();
	mushroomsSplit();
	storeLocator();
	believeCards();
	foundersCards();
	blogFilter();

	if (
		typeof window.Prism !== 'undefined' &&
		document.querySelector('code[class*="language-"], [class*="language-"] code')
	) {
		window.Prism.highlightAll();
	}
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initComponents, { once: true });
} else {
	initComponents();
}

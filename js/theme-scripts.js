;
console.log('Hello WordPress');
import {module1} from './components/module1';
import {module2} from './components/module2';
import mainMenu from './components/mainMenu';
import momentSlider from './components/momentSlider';
import productSlider from './components/productSlider';
import alliesSlider from './components/alliesSlider';
import storeLocator from './components/storeLocator';
import waysSlider from './components/waysSlider';
import foundersCards from './components/foundersCards';
import believeCards from './components/believeCards';
import reviewsSlider from './components/reviewsSlider';
import guideSlider from './components/guideSlider';
import ingredientsSlider from './components/ingredientsSlider';
import blogSlider from './components/blogSlider';
import blogFilter from './components/blogFilter';
import mainSlider from './components/mainSlider';

const initComponents = () => {
	mainMenu();
	mainSlider();
	momentSlider();
	productSlider();
	alliesSlider();
	storeLocator();
	waysSlider();
	believeCards();
	foundersCards();
	reviewsSlider();
	guideSlider();
	ingredientsSlider();
	blogSlider();
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

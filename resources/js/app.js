import './bootstrap';

import Alpine from 'alpinejs';
import { createApp } from 'vue';
import ChatbotApp from './components/ChatbotApp.vue';
import AnalyticsDashboard from './components/AnalyticsDashboard.vue';
import TrendsDashboard from './components/TrendsDashboard.vue';
import TrendsDetail from './components/TrendsDetail.vue';

window.Alpine = Alpine;

Alpine.start();

// Vue setup per componenti specifici
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing Vue components...');

    // Chatbot component
    const chatbotElement = document.getElementById('modern-chatbot');
    if (chatbotElement) {
        console.log('Found chatbot element, mounting Vue chatbot app...');
        try {
            // Ottieni i dati dalle data attributes o dal DOM
            const storeData = chatbotElement.dataset.store ? JSON.parse(chatbotElement.dataset.store) : null;
            const prefilledQuestion = chatbotElement.dataset.prefilledQuestion || null;
            const refCode = chatbotElement.dataset.refCode || null;

            console.log('Store data:', storeData);
            console.log('Prefilled question:', prefilledQuestion);
            console.log('Ref code:', refCode);

            const app = createApp(ChatbotApp, {
                store: storeData,
                prefilledQuestion: prefilledQuestion,
                refCode: refCode
            });

            app.mount('#modern-chatbot');
            console.log('Vue chatbot app mounted successfully');
        } catch (error) {
            console.error('Error mounting Vue chatbot app:', error);
            console.error('Error stack:', error.stack);
        }
    } else {
        console.log('No chatbot element found on this page');
    }

    // Analytics dashboard component
    const analyticsElement = document.getElementById('analytics-app');
    if (analyticsElement) {
        console.log('Mounting Vue analytics app...');
        try {
            const app = createApp({
                components: {
                    AnalyticsDashboard
                }
            });

            app.mount('#analytics-app');
            console.log('Vue analytics app mounted successfully');
        } catch (error) {
            console.error('Error mounting Vue analytics app:', error);
        }
    }

    // Trends dashboard component
    const trendsElement = document.getElementById('trends-dashboard-app');
    if (trendsElement) {
        console.log('Mounting Vue trends dashboard app...');
        try {
            const initialData = trendsElement.dataset.initialData ? JSON.parse(trendsElement.dataset.initialData) : {};

            const app = createApp(TrendsDashboard, {
                initialData: initialData
            });

            app.mount('#trends-dashboard-app');
            console.log('Vue trends dashboard app mounted successfully');
        } catch (error) {
            console.error('Error mounting Vue trends dashboard app:', error);
        }
    }

    // Trends detail component
    const trendsDetailElement = document.getElementById('trends-detail-app');
    if (trendsDetailElement) {
        console.log('Mounting Vue trends detail app...');
        try {
            const keyword = trendsDetailElement.dataset.keyword;
            const initialData = trendsDetailElement.dataset.initialData ? JSON.parse(trendsDetailElement.dataset.initialData) : {};

            const app = createApp(TrendsDetail, {
                keyword: keyword,
                initialData: initialData
            });

            app.mount('#trends-detail-app');
            console.log('Vue trends detail app mounted successfully');
        } catch (error) {
            console.error('Error mounting Vue trends detail app:', error);
        }
    }
});

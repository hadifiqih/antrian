import './bootstrap';
import '../css/app.css';
import axios from 'axios';
import lightGallery from 'lightgallery';
import lgThumbnail from 'lightgallery/plugins/thumbnail';
import lgZoom from 'lightgallery/plugins/zoom';
import { GoogleGenerativeAI } from '@google/generative-ai';

//init lightgallery
lightGallery(document.getElementById('lightgallery'), {
    plugins: [lgThumbnail, lgZoom],
    speed: 500,
    thumbnail: true,
});

// Fetch your API_KEY
const API_KEY = "AIzaSyArfgYuAN4t56Jwhya56eWl3gtxLgi2zZU";

// Access your API key (see "Set up your API key" above)
const genAI = new GoogleGenerativeAI(API_KEY);

// The Gemini 1.5 models are versatile and work with both text-only and multimodal prompts
const model = genAI.getGenerativeModel({ model: "gemini-1.5-flash"});



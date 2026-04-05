import './bootstrap';
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://ojtconnect.com:8000',
    withCredentials: true, // ✅ here
});

export default api
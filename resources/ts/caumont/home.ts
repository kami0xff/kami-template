import '../../css/caumont/home.css';
import { bootCaumontSite } from './site';

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => bootCaumontSite());
} else {
    bootCaumontSite();
}

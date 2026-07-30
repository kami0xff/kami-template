import '../../css/caumont/property-page.css';
import { bootCaumontProperty } from './property';

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => bootCaumontProperty());
} else {
    bootCaumontProperty();
}

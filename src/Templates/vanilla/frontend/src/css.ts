/* App App - Main Entry Point
 * Imports: Cascade Framework + custom styles
 */

// ===== CASCADE FRAMEWORK =====
import './cascade';

// ===== CUSTOM STYLES =====
// Shared styles (variables, reset, utilities, fonts)
import "./styles/components/reset.css";
import "./styles/components/fonts.css";
import "./styles/utilities.css";

// Component styles - truly shared across ALL pages
import "./styles/components/layout.css";
import "./styles/components/header.css";
import "./styles/components/footer.css";
import "./styles/components/button.css";
import "./styles/components/alert.css";
import "./styles/components/card.css";
import "./styles/components/htmx.css";
import "./styles/components/cookie-consent.css";
import "./styles/components/user-preferences.css";
import "./styles/components/anim-block.css";

// Page-specific styles that need to be loaded globally
import "./styles/login.css";
import "./styles/profile.css";

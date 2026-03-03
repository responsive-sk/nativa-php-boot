/* App App - Main Entry Point
 * Imports: shared styles + components (loaded on every page)
 */

// Shared styles (variables, reset, utilities, fonts)
import "./styles/shared/reset.css";
import "./styles/shared/fonts.css";
import "./styles/utilities.css";

// Component styles - truly shared across ALL pages
import "./styles/components/layout.css";
import "./styles/components/header.css";
import "./styles/components/footer.css";
import "./styles/components/button.css";
import "./styles/components/alert.css";
import "./styles/components/htmx.css";
import "./styles/components/cookie-consent.css";
import "./styles/components/user-preferences.css";
import "./styles/components/anim-block.css";

// Page-specific styles that need to be loaded globally
import "./styles/login.css";
import "./styles/profile.css";

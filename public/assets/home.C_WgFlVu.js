var v=Object.defineProperty,b=Object.defineProperties;var y=Object.getOwnPropertyDescriptors;var h=Object.getOwnPropertySymbols;var k=Object.prototype.hasOwnProperty,E=Object.prototype.propertyIsEnumerable;var p=(n,e,t)=>e in n?v(n,e,{enumerable:!0,configurable:!0,writable:!0,value:t}):n[e]=t,r=(n,e)=>{for(var t in e||(e={}))k.call(e,t)&&p(n,t,e[t]);if(h)for(var t of h(e))E.call(e,t)&&p(n,t,e[t]);return n},f=(n,e)=>b(n,y(e));import{i as S,a as C}from"./assets/index-BQ3NTUF8.js";function x(){document.querySelectorAll(".gallery-item").forEach(e=>{var i;const t=e.querySelector("img"),o=(i=e.querySelector(".gallery-overlay h3"))==null?void 0:i.textContent;t&&e.addEventListener("click",()=>{w(t.src,o)})})}function w(n,e){var c,l;const t=document.createElement("div");t.className="gallery-lightbox",t.innerHTML=`
        <div class="lightbox-backdrop"></div>
        <img src="${n}" alt="${e||""}">
        ${e?`<div class="lightbox-caption">${e}</div>`:""}
        <button class="lightbox-close">&times;</button>
    `,document.body.appendChild(t),requestAnimationFrame(()=>{t.classList.add("active")});const o=()=>{t.classList.remove("active"),setTimeout(()=>t.remove(),300)};(c=t.querySelector(".lightbox-backdrop"))==null||c.addEventListener("click",o),(l=t.querySelector(".lightbox-close"))==null||l.addEventListener("click",o);const i=g=>{g.key==="Escape"&&(o(),document.removeEventListener("keydown",i))};document.addEventListener("keydown",i)}function N(){document.querySelectorAll(".card").forEach(e=>{e.addEventListener("mouseenter",function(){this.classList.add("card--hovering")}),e.addEventListener("mouseleave",function(){this.classList.remove("card--hovering")})})}function d(n){try{return localStorage.getItem(n)}catch(e){return console.warn("localStorage not available:",e),null}}function u(n,e){try{localStorage.setItem(n,e)}catch(t){console.warn("localStorage not available:",t)}}const a=class a{static hasConsent(){return d(this.CONSENT_KEY)!==null}static getConsent(){const e=d(this.CONSENT_KEY);return e?JSON.parse(e):null}static setConsent(e){u(this.CONSENT_KEY,JSON.stringify(e))}static getUserInfo(){const e=d(this.USER_INFO_KEY);return e?JSON.parse(e):null}static setUserInfo(e){const t=this.getUserInfo()||{visitCount:0,lastVisit:new Date().toISOString(),preferences:{essential:!0,analytics:!1,marketing:!1,personalization:!1}},o=r(r({},t),e);return u(this.USER_INFO_KEY,JSON.stringify(o)),o}static incrementVisit(){const e=this.getUserInfo()||{visitCount:0,lastVisit:new Date().toISOString(),preferences:{essential:!0,analytics:!1,marketing:!1,personalization:!1}},t=f(r({},e),{visitCount:e.visitCount+1,lastVisit:new Date().toISOString()});return u(this.USER_INFO_KEY,JSON.stringify(t)),t}static shouldShowConsent(){return!this.hasConsent()&&d(this.CONSENT_SHOWN_KEY)!=="true"}static markConsentShown(){u(this.CONSENT_SHOWN_KEY,"true")}static getGreeting(){const e=this.getUserInfo();if(!e)return"Welcome!";const{name:t,visitCount:o}=e,i=new Date().getHours();let c="Hello";if(i<12?c="Good morning":i<18?c="Good afternoon":c="Good evening",t){const l=o===1?"first time":`${o}th time`;return`${c}, ${t}! Welcome back for your ${l}.`}return`${c}! Welcome back.`}};a.CONSENT_KEY="cookie_consent",a.USER_INFO_KEY="user_info",a.CONSENT_SHOWN_KEY="consent_shown";let s=a;function O(){const e=s.getConsent()||{analytics:!1,marketing:!1,personalization:!1},t=document.createElement("div");t.className="cookie-consent-overlay",t.innerHTML=`
    <div class="cookie-consent-modal">
      <div class="cookie-consent-header">
        <h3>
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 8px;">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 2a10 10 0 0 1 10 10 10 10 0 0 1-10 10"/>
            <path d="M12 2a10 10 0 0 0-10 10 10 10 0 0 0 10 10"/>
            <circle cx="8" cy="10" r="1.5"/>
            <circle cx="16" cy="10" r="1.5"/>
            <path d="M8 15h8"/>
          </svg>
          Cookie Preferences
        </h3>
        <p>We use cookies to enhance your experience and personalize your visit.</p>
      </div>

      <div class="cookie-consent-options">
        <div class="cookie-option">
          <label class="cookie-option-label">
            <input type="checkbox" checked disabled>
            <span>Essential</span>
            <small>Required for the site to function</small>
          </label>
        </div>

        <div class="cookie-option">
          <label class="cookie-option-label">
            <input type="checkbox" id="analytics" ${e.analytics?"checked":""}>
            <span>Analytics</span>
            <small>Help us improve the site</small>
          </label>
        </div>

        <div class="cookie-option">
          <label class="cookie-option-label">
            <input type="checkbox" id="marketing" ${e.marketing?"checked":""}>
            <span>Marketing</span>
            <small>For personalized ads</small>
          </label>
        </div>

        <div class="cookie-option">
          <label class="cookie-option-label">
            <input type="checkbox" id="personalization" ${e.personalization?"checked":""}>
            <span>Personalization</span>
            <small>Remember your preferences</small>
          </label>
        </div>
      </div>

      <div class="cookie-consent-actions">
        <button class="btn btn-outline" onclick="acceptEssential()">Essential Only</button>
        <button class="btn btn-outline" onclick="acceptSelected()">Accept Selected</button>
        <button class="btn btn--primary" onclick="acceptAll()">Accept All</button>
      </div>
    </div>
  `,document.body.appendChild(t),document.body.style.overflow="hidden",window.acceptEssential=()=>{const o={essential:!0,analytics:!1,marketing:!1,personalization:!1};s.setConsent(o),m()},window.acceptSelected=()=>{const o={essential:!0,analytics:document.getElementById("analytics").checked,marketing:document.getElementById("marketing").checked,personalization:document.getElementById("personalization").checked};s.setConsent(o),m()},window.acceptAll=()=>{const o={essential:!0,analytics:!0,marketing:!0,personalization:!0};s.setConsent(o),m()}}function m(){const n=document.querySelector(".cookie-consent-overlay");n&&(n.style.opacity="0",setTimeout(()=>{n.remove(),document.body.style.overflow=""},300))}function I(){const n=document.querySelectorAll("[data-greeting]"),e=s.getGreeting();n.forEach(t=>{t.textContent=e})}function L(){s.incrementVisit();const n=s.getConsent();n!=null&&n.personalization&&I(),s.shouldShowConsent()&&setTimeout(()=>{O(),s.markConsentShown()},1e3)}console.log("%c🏠 HOMEPAGE LOADING...","color: #d4af37; font-size: 14px; font-weight: bold");function A(){L(),x(),N(),S(),C(),_()}function _(){const n=document.querySelectorAll("[data-animate]"),e=new IntersectionObserver(t=>{t.forEach(o=>{o.isIntersecting&&(o.target.classList.add("is-visible"),e.unobserve(o.target))})},{threshold:.1,rootMargin:"0px 0px -100px 0px"});n.forEach(t=>e.observe(t))}document.addEventListener("DOMContentLoaded",()=>{A(),console.log("%c✅ HOMEPAGE READY","color: #10b981; font-size: 14px; font-weight: bold"),document.querySelectorAll('a[href^="#"]').forEach(n=>{n.addEventListener("click",function(e){const t=this.getAttribute("href");if(t&&t!=="#"){e.preventDefault();const o=document.querySelector(t);o&&o.scrollIntoView({behavior:"smooth",block:"start"})}})})});

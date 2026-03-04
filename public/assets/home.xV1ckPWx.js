var y=Object.defineProperty,b=Object.defineProperties;var k=Object.getOwnPropertyDescriptors;var f=Object.getOwnPropertySymbols;var E=Object.prototype.hasOwnProperty,S=Object.prototype.propertyIsEnumerable;var g=(n,e,t)=>e in n?y(n,e,{enumerable:!0,configurable:!0,writable:!0,value:t}):n[e]=t,u=(n,e)=>{for(var t in e||(e={}))E.call(e,t)&&g(n,t,e[t]);if(f)for(var t of f(e))S.call(e,t)&&g(n,t,e[t]);return n},v=(n,e)=>b(n,k(e));function C(){document.querySelectorAll(".gallery-item").forEach(e=>{var s;const t=e.querySelector("img"),o=(s=e.querySelector(".gallery-overlay h3"))==null?void 0:s.textContent;t&&e.addEventListener("click",()=>{w(t.src,o)})})}function w(n,e){var i,a;const t=document.createElement("div");t.className="gallery-lightbox",t.innerHTML=`
        <div class="lightbox-backdrop"></div>
        <img src="${n}" alt="${e||""}">
        ${e?`<div class="lightbox-caption">${e}</div>`:""}
        <button class="lightbox-close">&times;</button>
    `,document.body.appendChild(t),requestAnimationFrame(()=>{t.classList.add("active")});const o=()=>{t.classList.remove("active"),setTimeout(()=>t.remove(),300)};(i=t.querySelector(".lightbox-backdrop"))==null||i.addEventListener("click",o),(a=t.querySelector(".lightbox-close"))==null||a.addEventListener("click",o);const s=d=>{d.key==="Escape"&&(o(),document.removeEventListener("keydown",s))};document.addEventListener("keydown",s)}function x(){document.querySelectorAll(".card").forEach(e=>{e.addEventListener("mouseenter",function(){this.classList.add("card--hovering")}),e.addEventListener("mouseleave",function(){this.classList.remove("card--hovering")})})}function m(n){try{return localStorage.getItem(n)}catch(e){return console.warn("localStorage not available:",e),null}}function p(n,e){try{localStorage.setItem(n,e)}catch(t){console.warn("localStorage not available:",t)}}const r=class r{static hasConsent(){return m(this.CONSENT_KEY)!==null}static getConsent(){const e=m(this.CONSENT_KEY);return e?JSON.parse(e):null}static setConsent(e){p(this.CONSENT_KEY,JSON.stringify(e))}static getUserInfo(){const e=m(this.USER_INFO_KEY);return e?JSON.parse(e):null}static setUserInfo(e){const t=this.getUserInfo()||{visitCount:0,lastVisit:new Date().toISOString(),preferences:{essential:!0,analytics:!1,marketing:!1,personalization:!1}},o=u(u({},t),e);return p(this.USER_INFO_KEY,JSON.stringify(o)),o}static incrementVisit(){const e=this.getUserInfo()||{visitCount:0,lastVisit:new Date().toISOString(),preferences:{essential:!0,analytics:!1,marketing:!1,personalization:!1}},t=v(u({},e),{visitCount:e.visitCount+1,lastVisit:new Date().toISOString()});return p(this.USER_INFO_KEY,JSON.stringify(t)),t}static shouldShowConsent(){return!this.hasConsent()&&m(this.CONSENT_SHOWN_KEY)!=="true"}static markConsentShown(){p(this.CONSENT_SHOWN_KEY,"true")}static getGreeting(){const e=this.getUserInfo();if(!e)return"Welcome!";const{name:t,visitCount:o}=e,s=new Date().getHours();let i="Hello";if(s<12?i="Good morning":s<18?i="Good afternoon":i="Good evening",t){const a=o===1?"first time":`${o}th time`;return`${i}, ${t}! Welcome back for your ${a}.`}return`${i}! Welcome back.`}};r.CONSENT_KEY="cookie_consent",r.USER_INFO_KEY="user_info",r.CONSENT_SHOWN_KEY="consent_shown";let c=r;function N(){const e=c.getConsent()||{analytics:!1,marketing:!1,personalization:!1},t=document.createElement("div");t.className="cookie-consent-overlay",t.innerHTML=`
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
  `,document.body.appendChild(t),document.body.style.overflow="hidden",window.acceptEssential=()=>{const o={essential:!0,analytics:!1,marketing:!1,personalization:!1};c.setConsent(o),h()},window.acceptSelected=()=>{const o={essential:!0,analytics:document.getElementById("analytics").checked,marketing:document.getElementById("marketing").checked,personalization:document.getElementById("personalization").checked};c.setConsent(o),h()},window.acceptAll=()=>{const o={essential:!0,analytics:!0,marketing:!0,personalization:!0};c.setConsent(o),h()}}function h(){const n=document.querySelector(".cookie-consent-overlay");n&&(n.style.opacity="0",setTimeout(()=>{n.remove(),document.body.style.overflow=""},300))}function O(){const n=document.querySelectorAll("[data-greeting]"),e=c.getGreeting();n.forEach(t=>{t.textContent=e})}function I(){c.incrementVisit();const n=c.getConsent();n!=null&&n.personalization&&O(),c.shouldShowConsent()&&setTimeout(()=>{N(),c.markConsentShown()},1e3)}function L(n={}){var i,a;const e=(i=n.speed)!=null?i:.5,t=(a=n.breakpoint)!=null?a:768,o=document.querySelector(".app-hero");if(!o||window.innerWidth<=t)return;let s=!1;window.addEventListener("scroll",()=>{s||(window.requestAnimationFrame(()=>{const l=window.pageYOffset*-e;o.style.transform=`translate3d(0, ${l}px, 0)`,s=!1}),s=!0)})}function A(n={}){var t;const e=(t=n.animationDelay)!=null?t:.05;document.querySelectorAll(".text-gold-gradient").forEach(o=>{var i;const s=(i=o.textContent)!=null?i:"";o.innerHTML="",s.split("").forEach((a,d)=>{const l=document.createElement("span");l.textContent=a,l.style.animationDelay=`${d*e}s`,l.classList.add("gold-char"),o.appendChild(l)})})}console.log("%c🏠 HOMEPAGE LOADING...","color: #d4af37; font-size: 14px; font-weight: bold");function _(){I(),C(),x(),A(),L(),q()}function q(){const n=document.querySelectorAll("[data-animate]"),e=new IntersectionObserver(t=>{t.forEach(o=>{o.isIntersecting&&(o.target.classList.add("is-visible"),e.unobserve(o.target))})},{threshold:.1,rootMargin:"0px 0px -100px 0px"});n.forEach(t=>e.observe(t))}document.addEventListener("DOMContentLoaded",()=>{_(),console.log("%c✅ HOMEPAGE READY","color: #10b981; font-size: 14px; font-weight: bold"),document.querySelectorAll('a[href^="#"]').forEach(n=>{n.addEventListener("click",function(e){const t=this.getAttribute("href");if(t&&t!=="#"){e.preventDefault();const o=document.querySelector(t);o&&o.scrollIntoView({behavior:"smooth",block:"start"})}})})});

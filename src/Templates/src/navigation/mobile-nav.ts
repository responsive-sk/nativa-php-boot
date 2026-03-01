/**
 * Mobile Navigation Menu
 * Handles slide-in menu for mobile devices
 */

export interface MobileNavOptions {
  breakpoint?: number;
  animationDuration?: number;
}

export class MobileNav {
  private btn: HTMLButtonElement | null = null;
  private menu: HTMLElement | null = null;
  private links: NodeListOf<Element> | null = null;
  private isOpen = false;
  private readonly breakpoint: number;
  private readonly animationDuration: number;

  constructor(options: MobileNavOptions = {}) {
    this.breakpoint = options.breakpoint ?? 768;
    this.animationDuration = options.animationDuration ?? 250;
  }

  init(): void {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", () => this.setup());
    } else {
      this.setup();
    }
  }

  private setup(): void {
    this.btn = document.querySelector(".mobile-menu-btn");
    this.menu = document.querySelector(".mobile-menu");

    if (!this.btn || !this.menu) {
      console.warn("Mobile nav: button or menu not found");
      return;
    }

    this.menu.style.cssText = `
      position: fixed !important;
      top: 60px !important;
      left: 0 !important;
      right: 0 !important;
      bottom: 0 !important;
      z-index: 1000 !important;
      overflow-y: auto !important;
      -webkit-overflow-scrolling: touch !important;
      transform: translateX(-100%) !important;
      transition: transform ${this.animationDuration}ms ease, opacity ${this.animationDuration}ms ease !important;
      will-change: transform, opacity !important;
    `;

    this.links = this.menu.querySelectorAll(".mobile-menu__link");

    const boundToggle = this.toggle.bind(this);
    const boundClose = this.close.bind(this);

    this.btn.addEventListener("click", boundToggle, { passive: true });
    this.btn.addEventListener("touchend", (e: TouchEvent) => {
      e.preventDefault();
      boundToggle();
    }, { passive: false });

    this.links.forEach((link) => {
      link.addEventListener("click", () => {
        requestAnimationFrame(() => {
          boundClose();
        });
      }, { passive: true });

      link.addEventListener("touchend", () => {
        // Passive handler - allows default navigation
      }, { passive: true });
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && this.isOpen) {
        boundClose();
      }
    });

    document.addEventListener("click", (e: Event) => {
      const target = e.target as HTMLElement;
      if (
        this.isOpen &&
        !this.menu.contains(target) &&
        !this.btn.contains(target)
      ) {
        boundClose();
      }
    });

    let resizeTimer: number;
    window.addEventListener("resize", () => {
      window.clearTimeout(resizeTimer);
      resizeTimer = window.setTimeout(() => {
        if (window.innerWidth > this.breakpoint && this.isOpen) {
          boundClose();
        }
      }, 150);
    });

    this.setupScrollLock();
  }

  private setupScrollLock(): void {
    if (!this.menu) return;

    const observer = new MutationObserver(() => {
      if (this.isOpen) {
        document.body.style.overflow = "hidden";
        document.body.style.position = "fixed";
        document.body.style.width = "100%";
      } else {
        document.body.style.overflow = "";
        document.body.style.position = "";
        document.body.style.width = "";
      }
    });

    observer.observe(this.menu, {
      attributes: true,
      attributeFilter: ["class"]
    });
  }

  toggle(): void {
    if (this.isOpen) {
      this.close();
    } else {
      this.open();
    }
  }

  open(): void {
    if (!this.menu || !this.btn) {
      console.warn("Mobile nav: cannot open - missing elements");
      return;
    }

    this.isOpen = true;
    this.menu.classList.add("active");
    this.menu.style.transform = "translateX(0)";
    this.menu.style.opacity = "1";
    this.btn.setAttribute("aria-expanded", "true");
    this.btn.setAttribute("aria-label", "Close menu");

    void this.menu.offsetWidth;

    const firstLink = this.menu.querySelector("a") as HTMLElement;
    if (firstLink) {
      setTimeout(() => {
        try {
          firstLink.focus();
        } catch (e) {
          // Ignore focus errors on Android
        }
      }, this.animationDuration);
    }
  }

  close(): void {
    if (!this.menu || !this.btn) {
      console.warn("Mobile nav: cannot close - missing elements");
      return;
    }

    this.isOpen = false;
    this.menu.classList.remove("active");
    this.menu.style.transform = "translateX(-100%)";
    this.menu.style.opacity = "0";
    this.btn.setAttribute("aria-expanded", "false");
    this.btn.setAttribute("aria-label", "Open menu");

    setTimeout(() => {
      try {
        this.btn?.focus();
      } catch (e) {
        // Ignore focus errors on Android
      }
    }, this.animationDuration);
  }

  isOpenMenu(): boolean {
    return this.isOpen;
  }

  destroy(): void {
    if (this.btn) {
      this.btn.removeEventListener("click", this.toggle.bind(this));
      this.btn.removeEventListener("touchend", this.toggle.bind(this));
    }
  }
}

export function initMobileNav(): void {
  const mobileNav = new MobileNav();
  mobileNav.init();
  (window as any).mobileNav = mobileNav;
}

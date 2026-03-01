/**
 * Vitest Test Setup
 * Configures testing environment with custom matchers and mocks
 */

import "@testing-library/jest-dom/vitest";
import { afterEach, vi } from "vitest";

// Cleanup DOM after each test
afterEach(() => {
  document.body.innerHTML = "";
});

// Mock localStorage with proper 'this' binding
const localStorageMock = (function () {
  let store: Record<string, string> = {};

  return {
    getItem: vi.fn(function (key: string) {
      return store[key] || null;
    }),
    setItem: vi.fn(function (key: string, value: string) {
      store[key] = value;
    }),
    removeItem: vi.fn(function (key: string) {
      delete store[key];
    }),
    clear: vi.fn(function () {
      store = {};
    }),
    get store() {
      return store;
    },
  };
})();

Object.defineProperty(global, "localStorage", {
  value: localStorageMock,
  writable: true,
});

// Mock IntersectionObserver
class MockIntersectionObserver {
  observe = vi.fn();
  unobserve = vi.fn();
  disconnect = vi.fn();
}

Object.defineProperty(global, "IntersectionObserver", {
  value: MockIntersectionObserver,
  writable: true,
});

// Mock matchMedia
Object.defineProperty(global, "matchMedia", {
  writable: true,
  value: vi.fn().mockImplementation((query) => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: vi.fn(),
    removeListener: vi.fn(),
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
    dispatchEvent: vi.fn(),
  })),
});

// Mock requestAnimationFrame
Object.defineProperty(global, "requestAnimationFrame", {
  writable: true,
  value: vi.fn((cb) => setTimeout(cb, 0)),
});

// Mock scrollTo
Object.defineProperty(global, "scrollTo", {
  writable: true,
  value: vi.fn(),
});

// Mock element.animate for Web Animations API
Element.prototype.animate = vi.fn(function () {
  return {
    onfinish: null as (() => void) | null,
    finish: vi.fn(),
    cancel: vi.fn(),
    pause: vi.fn(),
    play: vi.fn(),
    reverse: vi.fn(),
    currentTime: 0,
    playbackRate: 1,
    startTime: 0,
    effect: null,
    id: "",
    pending: false,
    playState: "idle",
    replaceState: "active",
    commitStyles: vi.fn(),
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
    dispatchEvent: vi.fn(),
  };
}) as any;

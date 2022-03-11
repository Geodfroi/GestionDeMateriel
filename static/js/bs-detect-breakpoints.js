// https://github.com/mathiasayivor/bs-detect-breakpoint

/**
 * @type {Array}
 */
const breakpointNames = ["xxl", "xl", "lg", "md", "sm", "xs"];

/**
 * Bootstrap 5 doesn't have breakpoints defined
 * Defining fallback breakpoint values in case it's not defined by bootstrap
 *
 * @type {Object}
 */
let breakpointValues = {
  xxl: "1400px",
  xl: "1200px",
  lg: "992px",
  md: "768px",
  sm: "576px",
  xs: 0,
};

for (const breakpointName of breakpointNames) {
  const bsBreakpoint = window
    .getComputedStyle(document.documentElement)
    .getPropertyValue("--breakpoint-" + breakpointName)
    ?.trim();

  // Overriding fallback breakpoint if already defined
  if (typeof bsBreakpoint == "string" && bsBreakpoint !== "") {
    breakpointValues[breakpointName] = bsBreakpoint;
  }
}

/**
 * Returns the current breakpoint
 *
 * @returns {String}
 */
const getCurrentBreakpoint = () => {
  let i = breakpointNames.length;

  for (const breakpointName of breakpointNames) {
    i--;
    if (
      window.matchMedia("(min-width: " + breakpointValues[breakpointName] + ")")
        .matches
    ) {
      return { name: breakpointName, index: i };
    }
  }

  return null;
};

/**
 * Returns the current breakpoint
 *
 * @alias `getCurrentBreakpoint()`
 *
 * @deprecated Use `getCurrentBreakpoint()` instead
 *
 * @returns {String} @see `getCurrentBreakpoint()``
 */
const bootstrapDetectBreakpoint = () => {
  return getCurrentBreakpoint();
};

/**
 * Returns (in pixels) the value of the requested breakpoint
 *                    or null if not found
 * @description
 * @param {String} breakpointName
 * @returns {String|null}
 */
const getBreakpoint = (breakpointName) => {
  if (!breakpointNames.includes(breakpointName)) {
    return null;
  }

  return (breakpointValues[breakpointName] || null)?.trim();
};

let currentBreakpoint = getCurrentBreakpoint();

/**
 * Triggers events when initially called or a new breakpoint is reached
 *
 * @fires initEvent|newBreakpointEvent
 */
const init = () => {
  /**
   * @type object
   * @event bs.breakpoint.init
   */
  const initEvent = new Event("bs.breakpoint.init", {
    bubbles: true,
  });

  document.dispatchEvent(initEvent);

  window.onresize = function (e) {
    const newBreakpoint = getCurrentBreakpoint();

    if (newBreakpoint?.name !== currentBreakpoint?.name) {
      /**
       * @type object
       * @event bs.breakpoint.change
       */
      const newBreakpointEvent = new Event("bs.breakpoint.change", {
        bubbles: true,
      });

      document.dispatchEvent(newBreakpointEvent);
      currentBreakpoint = newBreakpoint;
    }
  };
};

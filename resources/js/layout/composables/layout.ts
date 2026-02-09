import { computed, reactive } from "vue";

interface LayoutConfig {
  preset: string;
  primary: string;
  surface: string;
  darkTheme: boolean;
  menuMode: string;
}

interface LayoutState {
  staticMenuInactive: boolean;
  overlayMenuActive: boolean;
  profileSidebarVisible: boolean;
  configSidebarVisible: boolean;
  sidebarExpanded: boolean;
  menuHoverActive: boolean;
  activeMenuItem: any;
  activePath: any;
  mobileMenuActive?: boolean;
  anchored?: boolean;
}

const layoutConfig = reactive<LayoutConfig>({
  preset: "Lara",
  primary: "orange",
  surface: "stone",
  darkTheme: false,
  menuMode: "static",
});

const layoutState = reactive<LayoutState>({
  staticMenuInactive: false,
  overlayMenuActive: false,
  profileSidebarVisible: false,
  configSidebarVisible: false,
  sidebarExpanded: false,
  menuHoverActive: false,
  activeMenuItem: null,
  activePath: null,
});

export function useLayout() {
  const toggleDarkMode = () => {
    if (!(document as any).startViewTransition) {
      executeDarkModeToggle();

      return;
    }

    (document as any).startViewTransition(() => executeDarkModeToggle());
  };

  const executeDarkModeToggle = () => {
    layoutConfig.darkTheme = !layoutConfig.darkTheme;
    document.documentElement.classList.toggle("app-dark");
  };

  const toggleMenu = () => {
    if (isDesktop()) {
      if (layoutConfig.menuMode === "static") {
        layoutState.staticMenuInactive = !layoutState.staticMenuInactive;
      }

      if (layoutConfig.menuMode === "overlay") {
        layoutState.overlayMenuActive = !layoutState.overlayMenuActive;
      }
    } else {
      layoutState.mobileMenuActive = !layoutState.mobileMenuActive;
    }
  };

  const toggleConfigSidebar = () => {
    layoutState.configSidebarVisible = !layoutState.configSidebarVisible;
  };

  const hideMobileMenu = () => {
    layoutState.mobileMenuActive = false;
  };

  const changeMenuMode = (event: any) => {
    layoutConfig.menuMode = event.value;
    layoutState.staticMenuInactive = false;
    layoutState.mobileMenuActive = false;
    layoutState.sidebarExpanded = false;
    layoutState.menuHoverActive = false;
    layoutState.anchored = false;
  };

  const isDarkTheme = computed(() => layoutConfig.darkTheme);
  const isDesktop = () => window.innerWidth > 991;

  const hasOpenOverlay = computed(() => layoutState.overlayMenuActive);

  return {
    layoutConfig,
    layoutState,
    isDarkTheme,
    toggleDarkMode,
    toggleConfigSidebar,
    toggleMenu,
    hideMobileMenu,
    changeMenuMode,
    isDesktop,
    hasOpenOverlay,
  };
}

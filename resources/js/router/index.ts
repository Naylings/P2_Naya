// import AppLayout from "@/layout/AppLayout.vue";

import AppLayout from "@/layout/AppLayout.vue";
import { useAuthStore } from "@/stores/auth";
import {
  createRouter,
  createWebHistory,
  type RouteRecordRaw,
} from "vue-router";

const routes: Array<RouteRecordRaw> = [
  {
    path: "/",
    meta: { requiresAuth: true },
    component: AppLayout,
    children: [
      {
        path: "/",
        name: "dashboard",
        component: () => import("@/views/Dashboard.vue"),
        meta: { title: "Dashboard" },
      },
      {
        path: "/pdf",
        name: "pdfForm",
        component: () => import("@/views/admin/pdf/PdfForm.vue"),
        meta: { title: "Form PDF", roles: ["administrator"] },
      },
      {
        path: "/users",
        name: "users",
        component: () => import("@/views/admin/users/Index.vue"),
        meta: { title: "Users" },
      },
      {
        path: "/rukun",
        name: "rukun",
        component: () => import("@/views/admin/rukun/Index.vue"),
        meta: { title: "Rukun", roles: ["administrator"] },
      },
      {
        path: "/family",
        name: "family",
        component: () => import("@/views/admin/family/Index.vue"),
        meta: { title: "Keluarga", roles: ["administrator"] },
      },
      {
        path: "/warga",
        name: "warga",
        component: () => import("@/views/admin/warga/Index.vue"),
        meta: { title: "Warga", roles: ["administrator"] },
      },
      {
        path: "/config",
        name: "config",
        component: () => import("@/views/admin/config/Index.vue"),
        meta: { title: "App Configuration", roles: ["administrator"] },
      },
      {
        path: "/uikit/formlayout",
        name: "formlayout",
        component: () => import("@/views/uikit/FormLayout.vue"),
        meta: { title: "Form Layout" },
      },
      {
        path: "/uikit/input",
        name: "input",
        component: () => import("@/views/uikit/InputDoc.vue"),
        meta: { title: "Input" },
      },
      {
        path: "/uikit/button",
        name: "button",
        component: () => import("@/views/uikit/ButtonDoc.vue"),
        meta: { title: "Button" },
      },
      {
        path: "/uikit/table",
        name: "table",
        component: () => import("@/views/uikit/TableDoc.vue"),
        meta: { title: "Table" },
      },
      {
        path: "/uikit/list",
        name: "list",
        component: () => import("@/views/uikit/ListDoc.vue"),
        meta: { title: "List" },
      },
      {
        path: "/uikit/tree",
        name: "tree",
        component: () => import("@/views/uikit/TreeDoc.vue"),
        meta: { title: "Tree" },
      },
      {
        path: "/uikit/panel",
        name: "panel",
        component: () => import("@/views/uikit/PanelsDoc.vue"),
        meta: { title: "Panel" },
      },
      {
        path: "/uikit/overlay",
        name: "overlay",
        component: () => import("@/views/uikit/OverlayDoc.vue"),
        meta: { title: "Overlay" },
      },
      {
        path: "/uikit/media",
        name: "media",
        component: () => import("@/views/uikit/MediaDoc.vue"),
        meta: { title: "Media" },
      },
      {
        path: "/uikit/message",
        name: "message",
        component: () => import("@/views/uikit/MessagesDoc.vue"),
        meta: { title: "Message" },
      },
      {
        path: "/uikit/file",
        name: "file",
        component: () => import("@/views/uikit/FileDoc.vue"),
        meta: { title: "File" },
      },
      {
        path: "/uikit/menu",
        name: "menu",
        component: () => import("@/views/uikit/MenuDoc.vue"),
        meta: { title: "Menu" },
      },
      {
        path: "/uikit/charts",
        name: "charts",
        component: () => import("@/views/uikit/ChartDoc.vue"),
        meta: { title: "Charts" },
      },
      {
        path: "/uikit/misc",
        name: "misc",
        component: () => import("@/views/uikit/MiscDoc.vue"),
        meta: { title: "Misc" },
      },
      {
        path: "/uikit/timeline",
        name: "timeline",
        component: () => import("@/views/uikit/TimelineDoc.vue"),
        meta: { title: "Timeline" },
      },
      {
        path: "/blocks/free",
        name: "blocks",
        component: () => import("@/views/utilities/Blocks.vue"),
        meta: { title: "Prime Blocks" },
      },
      {
        path: "/pages/empty",
        name: "empty",
        component: () => import("@/views/pages/Empty.vue"),
        meta: { title: "Empty Page" },
      },
      {
        path: "/pages/crud",
        name: "crud",
        component: () => import("@/views/pages/Crud.vue"),
        meta: { title: "CRUD" },
      },
    ],
  },
  {
    path: "/landing",
    name: "landing",
    component: () => import("@/views/pages/Landing.vue"),
    meta: { title: "Landing Page" },
  },
  {
    path: "/pages/notfound",
    name: "notfound",
    component: () => import("@/views/pages/NotFound.vue"),
    meta: { title: "Not Found" },
  },
  {
    path: "/auth/login",
    name: "login",
    component: () => import("@/views/pages/auth/Login.vue"),
    meta: { title: "Login" },
  },
  {
    path: "/auth/register",
    name: "register",
    component: () => import("@/views/pages/auth/Register.vue"),
    meta: { title: "Register" },
  },
  {
    path: "/auth/access",
    name: "accessDenied",
    component: () => import("@/views/pages/auth/Access.vue"),
    meta: { title: "Access Denied" },
  },
  {
    path: "/auth/error",
    name: "error",
    component: () => import("@/views/pages/auth/Error.vue"),
    meta: { title: "Error" },
  },
  {
    path: "/:pathMatch(.*)*",
    redirect: { name: "notfound" },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

const APP_NAME = "Naya";

router.beforeEach(async (to) => {
  try {
    const pageTitle = to.meta?.title as string;
    document.title = pageTitle ? `${APP_NAME} | ${pageTitle}` : APP_NAME;

    const token = localStorage.getItem("token");
    const auth = useAuthStore();

    // Jika route tidak ada
    if (to.matched.length === 0) {
      return { name: "notfound" };
    }

    // Jika butuh auth tapi tidak ada token
    if (to.meta.requiresAuth && !token) {
      return { name: "login" };
    }

    // Jika ada token tapi user belum di-load
    if (token && !auth.user) {
      try {
        await auth.fetchMe();
      } catch (error) {
        auth.clear();
        localStorage.removeItem("token");
        return { name: "login" };
      }
    }

    // Role-based access
    const allowedRoles = to.meta.roles as string[] | undefined;

    if (allowedRoles?.length) {
      if (!auth.user || !allowedRoles.includes(auth.user.role)) {
        return { name: "accessDenied" };
      }
    }

    // Jika sudah login lalu buka login/register
    if ((to.name === "login" || to.name === "register") && token) {
      return { name: "dashboard" };
    }

  } catch (e) {
    return { name: "error" };
  }
});


export default router;

export const API_BASE: string = import.meta.env.VITE_API_BASE_URL;

const endpoint = (path: string) => `${API_BASE}/${path}`;

export const API = {
  signup: endpoint("signup.php"),
  login: endpoint("login.php"),
  me: endpoint("me.php"),

  addOrder: endpoint("orders-add.php"),
  getOrders: endpoint("orders-get.php"),

  adminOrders: endpoint("admin-orders.php"),
  adminUpdateStatus: (id: number) => endpoint(`admin-order-status.php?id=${id}`),
  adminUpdateNotes: (id: number) => endpoint(`admin-order-notes.php?id=${id}`),

  pricingGet: endpoint("pricing-get.php"),

  pricingAdd: endpoint("admin-pricing-add.php"),
  pricingUpdate: (id: number) => endpoint(`admin-pricing-update.php?id=${id}`),
};

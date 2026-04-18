import { defineStore } from 'pinia'
import Toastify from 'toastify-js'
import 'toastify-js/src/toastify.css'

export const useCartStore = defineStore('cart', {
  state: () => ({
    cartItems: JSON.parse(localStorage.getItem('cartItems')) || [],
  }),
  getters: {
    totalItems: (state) => state.cartItems.reduce((total, item) => total + item.quantity, 0),
    totalPrice: (state) => state.cartItems.reduce((total, item) => total + (item.price * item.quantity), 0),
  },
  actions: {
    saveToLocalStorage() {
      localStorage.setItem('cartItems', JSON.stringify(this.cartItems))
    },
    addToCart(product) {
      const existingItem = this.cartItems.find(item => item.id === product.id)
      if (existingItem) {
        existingItem.quantity++
      } else {
        this.cartItems.push({ ...product, quantity: 1 })
      }
      this.saveToLocalStorage()

      const toastNode = document.createElement("div");
      toastNode.innerHTML = `
  <div class="flex items-center gap-4">
    
    <!-- Icono -->
    <div class="relative">
      <div class="absolute inset-0 bg-emerald-400 blur-md opacity-30 rounded-full"></div>
      <div class="relative bg-gradient-to-br from-emerald-400 to-emerald-600 p-2.5 rounded-xl shadow-lg">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
      </div>
    </div>

    <!-- Texto -->
    <div class="flex flex-col leading-tight">
      <span class="text-[10px] uppercase tracking-[0.25em] text-emerald-300 font-semibold">
        Carrito actualizado
      </span>
      <span class="text-sm font-semibold text-white">
        ${product.name}
      </span>
      <span class="text-xs text-white/60">
        Se añadió correctamente
      </span>
    </div>

  </div>
`;

      Toastify({
        node: toastNode,
        duration: 3000,
        close: false,
        gravity: "bottom",
        position: "right",
        stopOnFocus: true,
        style: {
          background: "linear-gradient(to right, #1a2e05, #628f2c)",
          borderRadius: "20px",
          padding: "16px 24px",
          boxShadow: "0 10px 25px -5px rgba(0, 0, 0, 0.3)",
          border: "1px solid rgba(255,255,255,0.1)"
        }
      }).showToast();
    },
    removeFromCart(id) {
      this.cartItems = this.cartItems.filter(item => item.id !== id)
      this.saveToLocalStorage()
    },
    updateQuantity(id, quantity) {
      const item = this.cartItems.find(item => item.id === id)
      if (item) {
        item.quantity = Math.max(1, quantity)
      }
      this.saveToLocalStorage()
    },
    clearCart() {
      this.cartItems = []
      this.saveToLocalStorage()
    },
    checkoutWhatsApp(userData) {
      if (this.cartItems.length === 0) return;

      const phoneNumber = "573115140908"; // Reemplazar con el número real
      const EMOJI = {
        cart: String.fromCodePoint(0x1F6D2),  // 🛒
        person: String.fromCodePoint(0x1F464),  // 👤
        box: String.fromCodePoint(0x1F4E6),  // 📦
        check: String.fromCodePoint(0x2705),   // ✅
        money: String.fromCodePoint(0x1F4B5),  // 💵
        truck: String.fromCodePoint(0x1F69A),  // 🚚
      };

      let message = `${EMOJI.cart} *NUEVO PEDIDO - CHAPARRO ECOMMERCE*\n\n`;

      message += `${EMOJI.person} *DATOS DEL CLIENTE*\n`;
      message += `• *Nombre:* ${userData.name}\n`;
      message += `• *Teléfono:* ${userData.phone}\n`;
      message += `• *Dirección:* ${userData.address}\n`;
      message += `• *Ciudad:* ${userData.city}\n`;
      if (userData.notes) message += `• *Notas:* ${userData.notes}\n`;

      message += `\n${EMOJI.box} *PRODUCTOS*\n`;
      this.cartItems.forEach(item => {
        const itemTotal = item.price * item.quantity;
        message += `${EMOJI.check} ${item.name} (x${item.quantity}) - $${Math.floor(itemTotal).toLocaleString('es-CO')}\n`;
      });

      message += `\n${EMOJI.money} *TOTAL A PAGAR: $${Math.floor(this.totalPrice).toLocaleString('es-CO')}*\n\n`;
      message += `${EMOJI.truck} _¡Quedo atento a la confirmación de mi pedido!_`;

      const encodedMessage = encodeURIComponent(message);

      // Notification
      const toastNode = document.createElement("div");
      toastNode.innerHTML = `
        <div class="flex items-center gap-4">
          <div class="relative">
            <div class="absolute inset-0 bg-emerald-400 blur-md opacity-30 rounded-full"></div>
            <div class="relative bg-gradient-to-br from-emerald-400 to-emerald-600 p-2.5 rounded-xl shadow-lg">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
          </div>
          <div class="flex flex-col leading-tight">
            <span class="text-[10px] uppercase tracking-[0.25em] text-emerald-300 font-semibold">Pedido Realizado</span>
            <span class="text-sm font-semibold text-white">¡Gracias por tu compra!</span>
            <span class="text-xs text-white/60">Redirigiendo a WhatsApp...</span>
          </div>
        </div>
      `;

      Toastify({
        node: toastNode,
        duration: 3000,
        gravity: "bottom",
        position: "right",
        style: {
          background: "linear-gradient(to right, #1a2e05, #628f2c)",
          borderRadius: "20px",
          padding: "16px 24px",
        }
      }).showToast();

      setTimeout(() => {
        const isMobile = /Android|iPhone|iPad/i.test(navigator.userAgent);

        const url = isMobile
          ? `https://wa.me/${phoneNumber}?text=${encodedMessage}`
          : `https://web.whatsapp.com/send?phone=${phoneNumber}&text=${encodedMessage}`;

        window.open(url, '_blank');
        this.clearCart();
      }, 1500);
    }
  }
})

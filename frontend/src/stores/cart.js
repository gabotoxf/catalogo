import { defineStore } from 'pinia'
import Toastify from 'toastify-js'
import 'toastify-js/src/toastify.css'
import { WHATSAPP_NUMBER } from '../utils/whatsapp'

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
  <div class="flex items-center gap-3">
    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shrink-0">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1B5E20" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
    </div>
    <div class="flex flex-col leading-tight">
      <span class="text-sm font-bold text-white">Añadido al carrito</span>
      <span class="text-xs text-white/70 truncate max-w-[220px]">${product.name}</span>
    </div>
  </div>
`;

      Toastify({
        node: toastNode,
        duration: 2500,
        close: false,
        gravity: "bottom",
        position: "right",
        stopOnFocus: true,
        style: {
          background: "#1B5E20",
          borderRadius: "16px",
          padding: "12px 16px",
          boxShadow: "0 12px 32px rgba(13,59,18,.45)",
          border: "1px solid rgba(255,255,255,.15)"
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

      const phoneNumber = WHATSAPP_NUMBER // centralizado en utils/whatsapp.js
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
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1B5E20" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
          </div>
          <div class="flex flex-col leading-tight">
            <span class="text-sm font-bold text-white">¡Pedido listo!</span>
            <span class="text-xs text-white/70">Te llevamos a WhatsApp…</span>
          </div>
        </div>
      `;

      Toastify({
        node: toastNode,
        duration: 3000,
        gravity: "bottom",
        position: "right",
        style: {
          background: "#1B5E20",
          borderRadius: "16px",
          padding: "12px 16px",
          boxShadow: "0 12px 32px rgba(13,59,18,.45)",
          border: "1px solid rgba(255,255,255,.15)"
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

/**
 * Bible Adventure — Confetti / Sparkles
 * Iteration 10: Lightweight celebration effect.
 * - Pure DOM, no library.
 * - Auto-cleanup after 3s.
 */
(function () {
  const COLORS = ["#f0b54a", "#4f9d69", "#4d82c4", "#cf645e", "#7868b8", "#5dade2"];

  function burst(count = 60) {
    const container = document.createElement("div");
    container.className = "confetti-container";
    document.body.appendChild(container);

    for (let i = 0; i < count; i++) {
      const piece = document.createElement("div");
      piece.className = "confetti-piece";
      piece.style.left = Math.random() * 100 + "vw";
      piece.style.background = COLORS[Math.floor(Math.random() * COLORS.length)];
      piece.style.animationDuration = 2 + Math.random() * 2 + "s";
      piece.style.animationDelay = Math.random() * 0.4 + "s";
      piece.style.transform = `rotate(${Math.random() * 360}deg)`;
      // Vary shape
      if (Math.random() > 0.6) {
        piece.style.borderRadius = "50%";
      }
      container.appendChild(piece);
    }

    setTimeout(() => {
      if (container.parentNode) container.parentNode.removeChild(container);
    }, 4500);
  }

  window.Confetti = { burst };
})();

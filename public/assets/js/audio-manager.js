/**
 * Bible Adventure — Audio Manager
 * Iteration 10: Lightweight, no-library audio manager.
 * - Lazy-loads Audio() only on first interaction (mobile autoplay safe).
 * - Honors user mute preference (localStorage).
 * - Synthesizes fallback beeps via WebAudio when no asset exists.
 */

(function () {
  const STORAGE_KEY = "ba.muted";
  const ASSETS = {
    correct: "/assets/audio/correct.mp3",
    wrong: "/assets/audio/wrong.mp3",
    complete: "/assets/audio/complete.mp3",
    checkpoint: "/assets/audio/checkpoint.mp3",
    click: "/assets/audio/click.mp3",
  };

  let muted = localStorage.getItem(STORAGE_KEY) === "1";
  let audioContext = null;
  const cache = new Map();

  function getContext() {
    if (audioContext) return audioContext;
    try {
      const Ctx = window.AudioContext || window.webkitAudioContext;
      if (Ctx) audioContext = new Ctx();
    } catch (e) {
      audioContext = null;
    }
    return audioContext;
  }

  function loadAudio(src) {
    if (cache.has(src)) return cache.get(src);
    const a = new Audio(src);
    a.preload = "auto";
    cache.set(src, a);
    return a;
  }

  // Synthesized fallback beeps (works offline, no asset needed)
  function beep(freq, duration, type = "sine", volume = 0.15) {
    const ctx = getContext();
    if (!ctx) return;
    try {
      if (ctx.state === "suspended") ctx.resume();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = type;
      osc.frequency.value = freq;
      gain.gain.setValueAtTime(volume, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + duration);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      osc.stop(ctx.currentTime + duration);
    } catch (e) {
      // WebAudio blocked — silent fail (visual feedback still shows)
    }
  }

  function play(name) {
    if (muted) return;
    // Try asset first
    const src = ASSETS[name];
    if (src) {
      try {
        const a = loadAudio(src);
        a.currentTime = 0;
        const p = a.play();
        if (p && typeof p.catch === "function") p.catch(() => synthesize(name));
        return;
      } catch (e) {
        synthesize(name);
      }
    } else {
      synthesize(name);
    }
  }

  function synthesize(name) {
    switch (name) {
      case "correct":
        beep(880, 0.1, "sine", 0.12);
        setTimeout(() => beep(1320, 0.18, "sine", 0.12), 90);
        break;
      case "wrong":
        beep(220, 0.18, "sawtooth", 0.1);
        break;
      case "complete":
        beep(523, 0.12, "sine", 0.12);
        setTimeout(() => beep(659, 0.12, "sine", 0.12), 120);
        setTimeout(() => beep(784, 0.18, "sine", 0.12), 240);
        setTimeout(() => beep(1047, 0.3, "sine", 0.14), 400);
        break;
      case "checkpoint":
        beep(660, 0.1, "triangle", 0.1);
        break;
      case "click":
        beep(440, 0.04, "square", 0.06);
        break;
      default:
        beep(440, 0.1, "sine", 0.1);
    }
  }

  function setMuted(value) {
    muted = !!value;
    localStorage.setItem(STORAGE_KEY, muted ? "1" : "0");
    document.dispatchEvent(
      new CustomEvent("ba:audio-mute-changed", { detail: { muted } })
    );
  }

  function toggle() {
    setMuted(!muted);
    // warm-up audio context on first user gesture
    getContext();
    return muted;
  }

  function isMuted() {
    return muted;
  }

  // Public API
  window.AudioManager = {
    play,
    toggle,
    setMuted,
    isMuted,
    get muted() {
      return muted;
    },
  };

  // Inject mute button on DOM ready
  function injectMuteButton() {
    if (document.getElementById("ba-audio-toggle")) return;
    const btn = document.createElement("button");
    btn.id = "ba-audio-toggle";
    btn.className = "audio-toggle" + (muted ? " muted" : "");
    btn.setAttribute("aria-label", muted ? "Aktifkan suara" : "Bisukan");
    btn.setAttribute("title", muted ? "🔇" : "🔊");
    btn.innerHTML = muted ? "🔇" : "🔊";
    btn.addEventListener("click", function () {
      const m = window.AudioManager.toggle();
      btn.classList.toggle("muted", m);
      btn.innerHTML = m ? "🔇" : "🔊";
      btn.setAttribute("aria-label", m ? "Aktifkan suara" : "Bisukan");
    });
    document.body.appendChild(btn);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", injectMuteButton);
  } else {
    injectMuteButton();
  }
})();

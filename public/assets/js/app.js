/**
 * Main Application Logic
 * Bible Adventure Sekolah Minggu GKJ Tangerang
 */

const App = {
  state: {
    selectedClass: null,
    currentStory: null,
    stories: [],
    eras: [],
  },

  async init() {
    this.container = document.getElementById("app");

    // Load data
    this.state.stories = await ApiService.getStories();
    this.state.eras = await ApiService.getEras();

    // Load progress
    this.state.selectedClass = ProgressService.getSelectedClass();

    if (!this.state.selectedClass) {
      this.showClassSelection();
    } else {
      this.showMap();
    }
  },

  showClassSelection() {
    this.container.innerHTML = `
            <div class="adventure-header">
                <h1>Bible Adventure</h1>
                <p>Perjalanan Besar Alkitab</p>
            </div>
            
            <h4 class="text-center mb-4" style="color: var(--game-text);">Pilih Kelasmu:</h4>
            
            <div class="class-card small game-card" onclick="App.selectClass('small')">
                <div class="class-icon">🌱</div>
                <h3>Kelas Kecil</h3>
                <p class="mb-0">SD Kelas 1–2</p>
            </div>
            
            <div class="class-card medium game-card" onclick="App.selectClass('medium')">
                <div class="class-icon">🌟</div>
                <h3>Kelas Madya</h3>
                <p class="mb-0">SD Kelas 3–4</p>
            </div>
            
            <div class="class-card large game-card" onclick="App.selectClass('large')">
                <div class="class-icon">🏆</div>
                <h3>Kelas Besar</h3>
                <p class="mb-0">SD Kelas 5–6</p>
            </div>
        `;
  },

  selectClass(classGroup) {
    this.state.selectedClass = classGroup;
    ProgressService.setSelectedClass(classGroup);
    this.showMap();
  },

  showMap() {
    const stats = ProgressService.getStats(this.state.stories);

    this.container.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="mb-0 text-game-primary">Bible Adventure Map</h2>
                    <span class="badge" style="background: ${this.getClassColor(this.state.selectedClass)}; font-size: 0.85rem;">
                        ${this.getClassName(this.state.selectedClass)}
                    </span>
                </div>
                <button class="btn btn-sm btn-light" onclick="App.showClassSelection()" style="border-radius: 50%; width: 40px; height: 40px; padding: 0;">
                    <i class="bi bi-arrow-left-circle" style="font-size: 1.5rem;"></i>
                </button>
            </div>
        `;

    const mapElement = MapComponent.render(this.state.stories, this.state.eras);
    this.container.appendChild(mapElement);

    const progressInfo = document.createElement("div");
    progressInfo.className = "mt-4 text-center";
    progressInfo.innerHTML = `
            <div class="game-card p-3">
                <p class="mb-1 fw-bold" style="color: var(--game-primary);">
                    ${stats.completed} dari ${stats.total} Perjalanan Selesai
                </p>
                <div class="mission-progress mt-2">
                    <div class="mission-progress-bar" style="width: ${stats.percentage}%"></div>
                </div>
            </div>
        `;
    this.container.appendChild(progressInfo);
  },

  async showStoryDetail(storyId) {
    // Check if story can be accessed
    if (!ProgressService.canAccessStory(storyId, this.state.stories)) {
      this.container.innerHTML = `
        <div class="text-center mt-5">
          <i class="bi bi-lock-fill" style="font-size: 4rem; color: var(--game-muted);"></i>
          <h3 class="mt-3">Cerita Terkunci</h3>
          <p class="text-muted">Selesaikan cerita sebelumnya terlebih dahulu.</p>
          <button class="game-btn game-btn-secondary mt-3" onclick="App.showMap()">
            <i class="bi bi-arrow-left-circle"></i> Kembali ke Map
          </button>
        </div>
      `;
      return;
    }

    const story = this.state.stories.find((s) => s.id === storyId);
    if (!story) {
      this.container.innerHTML = `
        <div class="text-center mt-5">
          <i class="bi bi-x-circle-fill" style="font-size: 4rem; color: var(--game-danger);"></i>
          <h3 class="mt-3">Cerita Tidak Ditemukan</h3>
          <button class="game-btn game-btn-secondary mt-3" onclick="App.showMap()">
            <i class="bi bi-arrow-left-circle"></i> Kembali ke Map
          </button>
        </div>
      `;
      return;
    }

    // Fetch class-specific content
    const content = await ApiService.getStoryContent(
      storyId,
      this.state.selectedClass,
    );

    if (!content) {
      this.container.innerHTML = `
        <div class="text-center mt-5">
          <i class="bi bi-exclamation-triangle-fill" style="font-size: 4rem; color: var(--game-warning);"></i>
          <h3 class="mt-3">Konten Belum Tersedia</h3>
          <p class="text-muted">Konten untuk kelas ini sedang dalam pengembangan.</p>
          <button class="game-btn game-btn-secondary mt-3" onclick="App.showMap()">
            <i class="bi bi-arrow-left-circle"></i> Kembali ke Map
          </button>
        </div>
      `;
      return;
    }

    this.state.currentStory = story;
    const timeline = ProgressService.getTimelineContext(
      storyId,
      this.state.stories,
    );
    const status = ProgressService.getStoryStatus(story, this.state.stories);

    this.container.innerHTML = `
            <div class="story-screen">
                <button class="btn btn-link p-0 mb-3 text-decoration-none" onclick="App.showMap()" style="color: var(--game-primary); font-weight: bold;">
                    <i class="bi bi-arrow-left-circle"></i> Kembali ke Map
                </button>
                
                <div class="game-card p-4 mb-4">
                    <div class="text-center mb-3">
                        <i class="bi bi-book-fill" style="font-size: 3rem; color: var(--game-accent);"></i>
                    </div>
                    <h2 class="text-center mb-3" style="color: var(--game-primary);">${story.title}</h2>
                    
                    ${
                      timeline
                        ? `
                    <div class="text-center mb-3 text-muted small">
                        ${timeline.previous ? timeline.previous.title + " →" : ""} 
                        <strong>${timeline.current.title}</strong>
                        ${timeline.next ? " → " + timeline.next.title : ""}
                    </div>
                    `
                        : ""
                    }
                    
                    <div class="mb-4">
                        <p style="font-size: 1.05rem; line-height: 1.7;">
                            ${content.summary}
                        </p>
                        <p class="text-muted small mt-3">
                            <i class="bi bi-bookmark-fill"></i> <em>Referensi: ${story.reference}</em>
                        </p>
                    </div>

                    ${
                      content.character_value
                        ? `
                    <div class="p-3 rounded mb-3" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-left: 4px solid var(--game-primary);">
                        <p class="mb-0"><i class="bi bi-heart-fill" style="color: var(--game-primary);"></i> <strong>Nilai Karakter:</strong> ${content.character_value}</p>
                    </div>
                    `
                        : ""
                    }
                    
                    ${
                      status === "completed"
                        ? `
                    <div class="p-3 rounded mb-3" style="background: #d4edda; border-left: 4px solid var(--game-success);">
                        <p class="mb-0"><i class="bi bi-check-circle-fill" style="color: var(--game-success);"></i> <strong>Cerita ini sudah selesai. Kamu bisa memainkannya lagi!</strong></p>
                    </div>
                    `
                        : `
                    <div class="p-3 rounded mb-3" style="background: #fff3cd; border-left: 4px solid var(--game-accent);">
                        <p class="mb-0"><i class="bi bi-lightbulb-fill" style="color: var(--game-accent);"></i> <strong>Selesaikan misi untuk membuka cerita selanjutnya!</strong></p>
                    </div>
                    `
                    }
                </div>
                
                <button class="game-btn game-btn-primary w-100" onclick="Game.startMisi('${storyId}', '${this.state.selectedClass}')">
                    <i class="bi bi-play-circle-fill"></i> ${status === "completed" ? "MAINKAN LAGI" : "MULAI MISI"}
                </button>
            </div>
        `;
  },

  getClassName(id) {
    const names = {
      small: "Kelas Kecil",
      medium: "Kelas Madya",
      large: "Kelas Besar",
    };
    return names[id] || id;
  },

  getClassColor(id) {
    const colors = {
      small: "#63a96b",
      medium: "#4c82c5",
      large: "#7868b8",
    };
    return colors[id] || "#6c757d";
  },
};

// Initialize app when DOM is ready
document.addEventListener("DOMContentLoaded", () => App.init());

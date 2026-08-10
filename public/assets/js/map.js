/**
 * Map Component
 * Data-driven Bible Adventure Map renderer
 */

const MapComponent = {
  render(stories, eras) {
    const wrapper = document.createElement("div");
    wrapper.className = "bible-map-container";

    const mapContainer = document.createElement("div");
    mapContainer.className = "bible-map";

    // Create SVG path for winding journey
    const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    svg.setAttribute("class", "map-path-svg");
    svg.setAttribute("viewBox", "0 0 1000 400");
    svg.setAttribute("preserveAspectRatio", "none");

    const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
    // Winding path from left to right
    path.setAttribute(
      "d",
      "M 80 200 Q 200 120, 280 200 T 480 200 Q 650 280, 800 200 L 920 200",
    );
    path.setAttribute("stroke", "#8d6e63");
    path.setAttribute("stroke-width", "12");
    path.setAttribute("fill", "none");
    path.setAttribute("stroke-linecap", "round");
    svg.appendChild(path);
    mapContainer.appendChild(svg);

    // Render checkpoints based on story data
    stories.forEach((story) => {
      const status = ProgressService.getStoryStatus(story, stories);
      const checkpoint = this.createCheckpoint(story, status);
      mapContainer.appendChild(checkpoint);
    });

    wrapper.appendChild(mapContainer);
    return wrapper;
  },

  createCheckpoint(story, status) {
    const checkpoint = document.createElement("div");
    checkpoint.className = `map-checkpoint ${status}`;
    checkpoint.setAttribute("data-story-id", story.id);
    checkpoint.setAttribute("role", "button");
    checkpoint.setAttribute("tabindex", "0");
    checkpoint.setAttribute(
      "aria-label",
      `${story.title}, ${this.getStatusLabel(status)}`,
    );

    // Use map_x and map_y from story data (convert to pixels based on map width)
    const mapWidth = 1000;
    const mapHeight = 400;
    const x = (story.map_x / 100) * mapWidth;
    const y = (story.map_y / 100) * mapHeight;

    checkpoint.style.left = `${x}px`;
    checkpoint.style.top = `${y}px`;
    checkpoint.style.transform = "translate(-50%, -50%)";

    const iconClass = this.getIcon(story.icon, status);
    checkpoint.innerHTML = `
                <i class="bi ${iconClass}" style="font-size: 2.2rem;"></i>
                <span class="checkpoint-label">${story.title}</span>
            `;

    // Add click handler based on status
    if (status !== "locked") {
      checkpoint.onclick = () => App.showStoryDetail(story.id);
      checkpoint.style.cursor = "pointer";

      // Keyboard accessibility
      checkpoint.onkeypress = (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          App.showStoryDetail(story.id);
        }
      };
    } else {
      checkpoint.onclick = () => this.showLockedMessage(story);
      checkpoint.style.cursor = "not-allowed";
    }

    return checkpoint;
  },

  showLockedMessage(story) {
    // Simple alert for locked story
    alert(
      `${story.title} masih terkunci.\n\nSelesaikan cerita sebelumnya terlebih dahulu.`,
    );
  },

  getStatusLabel(status) {
    const labels = {
      completed: "selesai",
      available: "dapat dimainkan",
      locked: "terkunci",
    };
    return labels[status] || status;
  },

  getIcon(storyIcon, status) {
    if (status === "locked") return "bi-lock-fill";
    if (status === "completed") return "bi-check-circle-fill";

    // Use icon from story data
    return `bi-${storyIcon}`;
  },
};

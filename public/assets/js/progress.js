/**
 * Progress Service
 * Centralized progress management using localStorage
 */

const ProgressService = {
  STORAGE_KEY: "bibleAdventureProgress",

  /**
   * Get full progress object
   */
  getProgress() {
    const data = localStorage.getItem(this.STORAGE_KEY);
    if (!data) {
      return this.getDefaultProgress();
    }
    try {
      return JSON.parse(data);
    } catch (error) {
      console.error("Progress parse error:", error);
      return this.getDefaultProgress();
    }
  },

  /**
   * Default progress structure
   */
  getDefaultProgress() {
    return {
      selectedClass: null,
      completedStories: [],
      lastStory: null,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    };
  },

  /**
   * Save progress to localStorage
   */
  saveProgress(progress) {
    progress.updatedAt = new Date().toISOString();
    localStorage.setItem(this.STORAGE_KEY, JSON.stringify(progress));
  },

  /**
   * Mark a story as completed
   */
  completeStory(storyId) {
    const progress = this.getProgress();
    if (!progress.completedStories.includes(storyId)) {
      progress.completedStories.push(storyId);
    }
    progress.lastStory = storyId;
    this.saveProgress(progress);
    return progress;
  },

  /**
   * Check if a story is completed
   */
  isStoryCompleted(storyId) {
    const progress = this.getProgress();
    return progress.completedStories.includes(storyId);
  },

  /**
   * Get completed story IDs
   */
  getCompletedStories() {
    const progress = this.getProgress();
    return progress.completedStories;
  },

  /**
   * Set selected class
   */
  setSelectedClass(classGroup) {
    const progress = this.getProgress();
    progress.selectedClass = classGroup;
    this.saveProgress(progress);
  },

  /**
   * Get selected class
   */
  getSelectedClass() {
    const progress = this.getProgress();
    return progress.selectedClass;
  },

  /**
   * Get last accessed story
   */
  getLastStory() {
    const progress = this.getProgress();
    return progress.lastStory;
  },

  /**
   * Calculate story status based on progress
   * @param {Object} story - Story object
   * @param {Array} allStories - All stories array (for finding previous)
   * @returns {string} - 'completed', 'available', or 'locked'
   */
  getStoryStatus(story, allStories) {
    const completedStories = this.getCompletedStories();

    // If story is completed
    if (completedStories.includes(story.id)) {
      return "completed";
    }

    // If this is the first story
    if (!story.previous_id) {
      return "available";
    }

    // Check if previous story is completed
    if (completedStories.includes(story.previous_id)) {
      return "available";
    }

    // Otherwise locked
    return "locked";
  },

  /**
   * Get timeline context for a story (previous, current, next)
   */
  getTimelineContext(storyId, allStories) {
    const story = allStories.find((s) => s.id === storyId);
    if (!story) return null;

    const previous = story.previous_id
      ? allStories.find((s) => s.id === story.previous_id)
      : null;
    const next = story.next_id
      ? allStories.find((s) => s.id === story.next_id)
      : null;

    return {
      previous: previous ? { id: previous.id, title: previous.title } : null,
      current: { id: story.id, title: story.title },
      next: next ? { id: next.id, title: next.title } : null,
    };
  },

  /**
   * Reset all progress (for testing/debugging)
   */
  resetProgress() {
    localStorage.removeItem(this.STORAGE_KEY);
  },

  /**
   * Check if user can access a story
   */
  canAccessStory(storyId, allStories) {
    const story = allStories.find((s) => s.id === storyId);
    if (!story) return false;

    const status = this.getStoryStatus(story, allStories);
    return status === "available" || status === "completed";
  },

  /**
   * Get progress statistics
   */
  getStats(allStories) {
    const completedStories = this.getCompletedStories();
    const totalStories = allStories.length;
    const completedCount = completedStories.length;
    const percentage =
      totalStories > 0 ? Math.round((completedCount / totalStories) * 100) : 0;

    return {
      total: totalStories,
      completed: completedCount,
      remaining: totalStories - completedCount,
      percentage: percentage,
    };
  },
};

/**
 * NursesPro Academy - Curriculum Data
 * Fetches the real curriculum_modules table via api/curriculum.php.
 * Call `await initCurriculum()` once at the top of a page's init flow, then
 * the synchronous getters below read from the cache it populates.
 */

let CURRICULUM_MODULES = [];

async function initCurriculum() {
  const res = await fetch('api/curriculum.php').then(r => r.json());
  CURRICULUM_MODULES = res.modules || [];
  return CURRICULUM_MODULES;
}

function getCurriculumModules(course, year, semester) {
  const mods = CURRICULUM_MODULES.filter(m => m.course === course && m.year === year && m.semester === semester);
  return mods.length ? mods : null;
}

// Flat list of every module across every course/year/semester, for admin dropdowns.
function getAllModules() {
  return CURRICULUM_MODULES;
}

// Look up a module's title/course/year/semester by its code (e.g. 'DNE 111').
function getModuleInfo(code) {
  return CURRICULUM_MODULES.find(m => m.code === code) || null;
}

# Task Manager - Qwen Skill

Task management skill for Qwen Code with SQLite backend, progress tracking, and PHP project scanning.

**Supports multiple interfaces:**
- **CLI** (`tm` commands)
- **MCP** (direct AI agent tools)
- **Web UI** (browser interface)
- **REST API** (HTTP endpoints)

## Installation

Skill is automatically available when you have the task_manager project in your workspace.

**Manual install:**
```bash
cp -r ~/task-manager/.qwen/skills/task-manager ~/.qwen/skills/
```

## Usage

### CLI Commands

```bash
# Projects
tm init                      # Create new project (interactive)
tm list                      # List all projects
tm stats <project>           # Show project statistics

# Tasks
tm add <project> <title> [-d desc] [-p priority] [-t tags]
tm start <task_id>           # Mark as in_progress
tm done <task_id> [-n notes] # Mark as completed
tm block <task_id> [-r reason] # Mark as blocked
tm next <project>            # Get next pending task
tm tasks <project> [-s status] # List tasks

# Agent State
tm save <project> -i "instructions"  # Save agent state
tm resume <project>          # Generate resume instructions

# Archive
tm archive [--days N] [--dry-run]     # Archive old completed tasks
tm archive-list [--year Y] [--project P]  # List archived tasks
tm archive-restore <id> --year Y      # Restore from archive
```

### MCP Tools

All functions are available via MCP for AI agent integration:

```bash
# Project management
mcp__task-manager__create_project
mcp__task-manager__list_projects
mcp__task-manager__get_project_info
mcp__task-manager__get_project_stats

# Task management
mcp__task-manager__create_task
mcp__task-manager__update_task_status
mcp__task-manager__get_next_task
mcp__task-manager__list_tasks
mcp__task-manager__search_tasks
mcp__task-manager__delete_task

# Agent state
mcp__task-manager__save_agent_state
mcp__task-manager__resume_agent_state
mcp__task-manager__clear_agent_state

# File operations
mcp__task-manager__read_file
mcp__task-manager__write_file
mcp__task-manager__edit_file
mcp__task-manager__list_directory
mcp__task-manager__file_exists
mcp__task-manager__search_code
```

### Web UI

```bash
tm-web
# Open http://localhost:5000
```

### REST API

```bash
tm-api
# Endpoints:
# GET  /projects      - List projects
# GET  /next/<id>     - Get next task
# GET  /resume/<id>   - Resume instructions
# POST /complete/<id> - Complete task
# POST /context/<id>  - Save agent state
```

## Basic Commands

```
/task-manager init
/task-manager add MyProject "Task title"
/task-manager list
/task-manager next MyProject
/task-manager start <id>
/task-manager done <id>
```

### Advanced Commands

```
/task-manager scan /path/to/php-project -p ProjectName
/task-manager save MyProject
/task-manager resume MyProject
/task-manager stats MyProject
```

## Examples

### Create New Project

```
I want to create a new project called "WebApp" for building a React + Node.js application.
```

Qwen will:
1. Run `tm init` or create project directly
2. Ask for description
3. Suggest initial tasks

### Add Tasks

```
Add a task to implement user authentication with priority 10 and tags backend,security
```

Qwen will:
1. Run `tm add WebApp "Implement user authentication" --priority 10 --tags backend security`
2. Confirm task creation

### Work on Tasks

```
What should I work on next?
```

Qwen will:
1. Run `tm next WebApp`
2. Show you the highest priority pending task

### PHP Project Scan

```
Scan my PHP project at /var/www/myapp and create tasks for issues found
```

Qwen will:
1. Run `tm scan /var/www/myapp -p MyApp`
2. Show found issues
3. List created tasks

### Save Progress

```
I need to stop working. Save my progress.
```

Qwen will:
1. Ask for brief instructions for resumption
2. Run `tm save WebApp -i "instructions"`
3. Save context for next session

## Task Manager CLI Reference

For full CLI documentation, see: `/path/to/task_manager/README.md`

## Database

Tasks are stored in: `~/.task_manager.db`

You can query directly:
```bash
sqlite3 ~/.task_manager.db "SELECT * FROM tasks WHERE status='pending' ORDER BY priority DESC;"
```

## Integration with Other Skills

This skill works well with:
- Code generation skills - create tasks for generated features
- Testing skills - create tasks for test coverage
- Security skills - create tasks from security findings

## Tips

1. **Use priorities** - Not everything is priority 10!
2. **Add tags** - Makes filtering easier: `backend`, `frontend`, `bug`, `feature`
3. **Save often** - Use `tm save` before stopping
4. **Review stats** - `tm stats` shows your progress

## Troubleshooting

**Skill not found:**
```bash
# Verify installation
ls ~/.qwen/skills/task-manager/SKILL.md

# Restart Qwen Code
```

**Database locked:**
```bash
# Check for other processes
lsof ~/.task_manager.db

# Restart terminal if needed
```

**Commands not working:**
```bash
# Verify tm is in PATH
which tm
```

## Task History Feature

View complete history of task status changes with timeline visualization:

```bash
# View task history via CLI
tm task history <id>

# View in Web UI - click on any task to see timeline
```

**Example output:**
```
Task History: #98 - Test task history feature
   Status: completed
   Created: 2026-02-19 17:19:50
   Completed: 2026-02-19T18:20:08

Progress Log:
   [2026-02-19 17:19:50] created
      Task created: Test task history feature
   [2026-02-19 17:19:55] status_changed_in_progress
      Status: pending -> in_progress | Starting to test
   [2026-02-19 17:20:08] status_changed_completed
      Status: in_progress -> completed | Duration: 1:00:18
```

**Web UI Timeline:**
- Visual timeline with connecting line and dots
- Icons for events: created, started, completed, blocked
- Automatic duration calculation for completed tasks
- Detailed progress log with timestamps

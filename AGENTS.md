# Best Practices for Claude Harness in Software Engineering

## Managing Long-Running Agents

When working with Claude for complex software engineering tasks that span multiple sessions, implement these strategies to maintain continuity and effectiveness:

### Environment Setup
- Create an initialization script (init.sh) to consistently set up the development environment
- Establish a progress tracking file (claude-progress.txt) to document what tasks have been completed
- Initialize a git repository with an initial commit showing baseline files

### Feature Management
- Create a comprehensive JSON feature list file with all requirements broken down into testable units
- Structure features with clear categories, descriptions, steps, and pass/fail status
- Allow Claude to only update the "passes" field, preventing modification of requirements
- Focus on one feature at a time to avoid overwhelming complexity

### Incremental Progress
- Require Claude to leave the codebase in a "clean state" after each session - code appropriate for merging to main with no major bugs
- Use git commits with descriptive messages to track progress
- Write summaries in progress files to enable smooth handoffs between sessions
- Allow Claude to use git to revert problematic changes

### Testing & Verification
- Implement end-to-end testing using browser automation tools (like Puppeteer) to verify features work as a human user would experience them
- Capture screenshots during testing to help identify issues
- Perform basic functionality checks at the start of each session to ensure the app isn't in a broken state
- Verify the development server can be properly restarted using the init.sh script

### Session Initialization
- Begin each session by checking the current directory and working environment
- Read git logs and progress files to understand the current state
- Review the feature list file and select the highest-priority incomplete feature
- Run basic end-to-end tests to confirm the application functions properly before implementing new features

### Common Failure Mode Solutions
- **Premature completion**: Use structured feature lists and single-feature focus
- **Environment inconsistencies**: Implement initialization scripts and progress tracking
- **Unverified completions**: Require testing verification before marking features complete
- **Setup confusion**: Provide clear init.sh scripts for all sessions

## Prompt Design Strategies for Coding Tasks

### Clear Instructions
- Give explicit, specific instructions about what you want Claude to accomplish
- Break complex tasks into smaller, manageable steps
- Include specific technical requirements and constraints

### Role Assignment
- Define the role Claude should assume (e.g., "senior software engineer", "full-stack developer")
- Specify the technology stack and project context

### Context Provision
- Provide relevant project documentation, code structure, and existing files
- Share coding standards, architecture patterns, and project conventions
- Include dependency information and configuration details

### Output Format Requirements
- Specify the desired output format (JSON, Markdown, code files, etc.)
- Define file structure and naming conventions
- Request specific documentation or comments if needed

### Iterative Development
- Design prompts that support iterative improvement
- Include verification steps within the prompt structure
- Plan for multiple interaction rounds to refine the implementation

## Quality Assurance Practices

### Code Quality
- Verify that code follows project conventions and style guides
- Run project-specific linters and formatters
- Execute tests to ensure functionality hasn't been broken
- Check that code integrates properly with existing systems

### Safety Measures
- Explain the purpose of commands before executing shell operations
- Apply security best practices to avoid exposing sensitive information
- Use absolute paths and verify file operations before executing
- Maintain awareness of system state and potential side effects

### Progress Tracking
- Document completed work in progress files
- Use git to track changes and maintain version history
- Keep clear records of decisions and approaches taken
- Maintain clean commit history for easier review and rollback

## Operational Guidelines

### Tool Usage
- Leverage appropriate tools for different tasks (read_file, edit, grep_search, etc.)
- Use absolute paths for all file operations
- Execute multiple independent tool calls in parallel when possible
- Use background processes appropriately for long-running operations

### Task Management
- Use todo tracking for complex, multi-step tasks
- Break large tasks into smaller, manageable components
- Update task status in real-time as work progresses
- Focus on one primary task at a time to maintain clarity

### Communication
- Provide concise, direct responses suitable for CLI environment
- Focus on technical output rather than conversational elements
- Use GitHub-flavored Markdown for formatting when needed
- Maintain professional, efficient communication style

### Commiting changes
- When commiting changes, never mentions any Co-authoring

## Issue Tracking with bd (beads)

**IMPORTANT**: This project uses **bd (beads)** for ALL issue tracking. Do NOT use markdown TODOs, task lists, or other tracking methods.

### Why bd?

- Dependency-aware: Track blockers and relationships between issues
- Git-friendly: Auto-syncs to JSONL for version control
- Agent-optimized: JSON output, ready work detection, discovered-from links
- Prevents duplicate tracking systems and confusion

### Quick Start

**Check for ready work:**
```bash
bd ready --json
```

**Create new issues:**
```bash
bd create "Issue title" -t bug|feature|task -p 0-4 --json
bd create "Issue title" -p 1 --deps discovered-from:bd-123 --json
bd create "Subtask" --parent <epic-id> --json  # Hierarchical subtask (gets ID like epic-id.1)
```

**Claim and update:**
```bash
bd update bd-42 --status in_progress --json
bd update bd-42 --priority 1 --json
```

**Complete work:**
```bash
bd close bd-42 --reason "Completed" --json
```

### Issue Types

- `bug` - Something broken
- `feature` - New functionality
- `task` - Work item (tests, docs, refactoring)
- `epic` - Large feature with subtasks
- `chore` - Maintenance (dependencies, tooling)

### Priorities

- `0` - Critical (security, data loss, broken builds)
- `1` - High (major features, important bugs)
- `2` - Medium (default, nice-to-have)
- `3` - Low (polish, optimization)
- `4` - Backlog (future ideas)

### Workflow for AI Agents

1. **Check ready work**: `bd ready` shows unblocked issues
2. **Claim your task**: `bd update <id> --status in_progress`
3. **Work on it**: Implement, test, document
4. **Discover new work?** Create linked issue:
   - `bd create "Found bug" -p 1 --deps discovered-from:<parent-id>`
5. **Complete**: `bd close <id> --reason "Done"`
6. **Commit together**: Always commit the `.beads/issues.jsonl` file together with the code changes so issue state stays in sync with code state

### Auto-Sync

bd automatically syncs with git:
- Exports to `.beads/issues.jsonl` after changes (5s debounce)
- Imports from JSONL when newer (e.g., after `git pull`)
- No manual export/import needed!

### GitHub Copilot Integration

If using GitHub Copilot, also create `.github/copilot-instructions.md` for automatic instruction loading.
Run `bd onboard` to get the content, or see step 2 of the onboard instructions.

### MCP Server (Recommended)

If using Claude or MCP-compatible clients, install the beads MCP server:

```bash
pip install beads-mcp
```

Add to MCP config (e.g., `~/.config/claude/config.json`):
```json
{
  "beads": {
    "command": "beads-mcp",
    "args": []
  }
}
```

Then use `mcp__beads__*` functions instead of CLI commands.

### Managing AI-Generated Planning Documents

AI assistants often create planning and design documents during development:
- PLAN.md, IMPLEMENTATION.md, ARCHITECTURE.md
- DESIGN.md, CODEBASE_SUMMARY.md, INTEGRATION_PLAN.md
- TESTING_GUIDE.md, TECHNICAL_DESIGN.md, and similar files

**Best Practice: Use a dedicated directory for these ephemeral files**

**Recommended approach:**
- Create a `history/` directory in the project root
- Store ALL AI-generated planning/design docs in `history/`
- Keep the repository root clean and focused on permanent project files
- Only access `history/` when explicitly asked to review past planning

**Example .gitignore entry (optional):**
```
# AI planning documents (ephemeral)
history/
```

**Benefits:**
- ✅ Clean repository root
- ✅ Clear separation between ephemeral and permanent documentation
- ✅ Easy to exclude from version control if desired
- ✅ Preserves planning history for archeological research
- ✅ Reduces noise when browsing the project

### CLI Help

Run `bd <command> --help` to see all available flags for any command.
For example: `bd create --help` shows `--parent`, `--deps`, `--assignee`, etc.

### Important Rules

- ✅ Use bd for ALL task tracking
- ✅ Always use `--json` flag for programmatic use
- ✅ Link discovered work with `discovered-from` dependencies
- ✅ Check `bd ready` before asking "what should I work on?"
- ✅ Store AI planning docs in `history/` directory
- ✅ Run `bd <cmd> --help` to discover available flags
- ❌ Do NOT create markdown TODO lists
- ❌ Do NOT use external issue trackers
- ❌ Do NOT duplicate tracking systems
- ❌ Do NOT clutter repo root with planning documents

For more details, see README.md and QUICKSTART.md.
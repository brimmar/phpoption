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

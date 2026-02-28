#!/bin/bash
# Quick script to remove "Co-authored-by: Qwen-Coder" from recent commits
# Uses interactive rebase with exec for automated cleanup

set -e

YELLOW='\033[1;33m'
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}=== Quick Cleanup: Last 10 Commits ===${NC}"
echo ""

# Check if we're in a git repository
if ! git rev-parse --git-dir > /dev/null 2>&1; then
    echo -e "${RED}Error: Not in a git repository${NC}"
    exit 1
fi

# Check for the pattern
FOUND=$(git log --oneline -10 --grep="Co-authored-by: Qwen-Coder" 2>/dev/null)

if [ -z "$FOUND" ]; then
    echo -e "${GREEN}✓ No commits found in last 10 with 'Co-authored-by: Qwen-Coder'${NC}"
    exit 0
fi

echo "Found commits to clean:"
echo "$FOUND"
echo ""

# Create backup
BACKUP_BRANCH="backup-qwen-cleanup-$(date +%Y%m%d-%H%M%S)"
echo "Creating backup: $BACKUP_BRANCH"
git branch "$BACKUP_BRANCH"

echo ""
echo -e "${YELLOW}Starting interactive rebase...${NC}"
echo -e "${YELLOW}For each commit with 'Co-authored-by', change 'pick' to 'reword'${NC}"
echo -e "${YELLOW}Then remove the 'Co-authored-by: Qwen-Coder' line in the editor${NC}"
echo ""
echo "Backup branch: $BACKUP_BRANCH"
echo ""

# Start interactive rebase
GIT_SEQUENCE_EDITOR="sed -i 's/^pick /reword /g'" git rebase -i HEAD~10

echo ""
echo -e "${GREEN}✓ Rebase complete!${NC}"
echo "Verify with: git log --oneline"

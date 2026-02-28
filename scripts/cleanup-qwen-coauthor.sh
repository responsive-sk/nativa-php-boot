#!/bin/bash
# Script to remove "Co-authored-by: Qwen-Coder" from existing git commits
# Uses git filter-branch to rewrite commit history

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}=== Git Commit Message Cleanup ===${NC}"
echo "This script will remove 'Co-authored-by: Qwen-Coder' from your git history."
echo ""

# Check if we're in a git repository
if ! git rev-parse --git-dir > /dev/null 2>&1; then
    echo -e "${RED}Error: Not in a git repository${NC}"
    exit 1
fi

# Check for the pattern in recent commits
echo "Searching for commits with 'Co-authored-by: Qwen-Coder'..."
FOUND=$(git log --oneline --all --grep="Co-authored-by: Qwen-Coder" 2>/dev/null | head -20)

if [ -z "$FOUND" ]; then
    echo -e "${GREEN}✓ No commits found with 'Co-authored-by: Qwen-Coder'${NC}"
    exit 0
fi

echo ""
echo -e "${YELLOW}Found commits:${NC}"
echo "$FOUND"
echo ""

# Count affected commits
COUNT=$(git log --oneline --all --grep="Co-authored-by: Qwen-Coder" 2>/dev/null | wc -l)
echo "Total commits affected: $COUNT"
echo ""

# Ask for confirmation
echo -e "${RED}⚠️  WARNING: This will rewrite git history!${NC}"
echo "After running this script, you will need to force push to remote repositories."
echo ""
read -p "Do you want to continue? (y/N): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Aborted."
    exit 0
fi

# Ask about scope
echo ""
echo "Select scope:"
echo "1) Last 5 commits"
echo "2) Last 10 commits"
echo "3) Last 20 commits"
echo "4) All commits (entire history)"
read -p "Enter choice [1-4] (default: 2): " SCOPE_CHOICE

case ${SCOPE_CHOICE:-2} in
    1) HEAD_N="HEAD~5" ;;
    2) HEAD_N="HEAD~10" ;;
    3) HEAD_N="HEAD~20" ;;
    4) HEAD_N="--all" ;;
    *) HEAD_N="HEAD~10" ;;
esac

echo ""
echo -e "${YELLOW}Running git filter-branch...${NC}"

# Create backup branch
BACKUP_BRANCH="backup-before-qwen-cleanup-$(date +%Y%m%d-%H%M%S)"
echo "Creating backup branch: $BACKUP_BRANCH"
git branch "$BACKUP_BRANCH"

# Run filter-branch to rewrite commit messages
if [ "$HEAD_N" = "--all" ]; then
    git filter-branch --msg-filter 'grep -v "^Co-authored-by: Qwen-Coder"' -- --all
else
    git filter-branch --msg-filter 'grep -v "^Co-authored-by: Qwen-Coder"' -- "$HEAD_N"..HEAD
fi

echo ""
echo -e "${GREEN}✓ Cleanup complete!${NC}"
echo ""
echo "Backup created at: $BACKUP_BRANCH"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Verify the changes: git log --oneline"
echo "2. If satisfied, force push: git push --force --force-with-lease"
echo "3. If something went wrong, restore from backup:"
echo "   git reset --hard $BACKUP_BRANCH"
echo ""
echo -e "${RED}Note: If you have a remote repository, you MUST force push:${NC}"
echo "  git push --force --force-with-lease origin"
